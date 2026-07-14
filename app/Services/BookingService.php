<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Payment;
use App\Models\Show;
use App\Models\ShowSeat;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class BookingService
{
    public function __construct(protected WalletService $walletService)
    {
    }

    /*
      Temporarily lock seats for a user while they're on the booking summary
      page. Locks expire after 10 minutes (see ShowSeat::isBookable()).
     */
    public function lockSeats(Show $show, array $seatCodes, User $user): array
    {
        return DB::transaction(function () use ($show, $seatCodes, $user) {
            $seats = ShowSeat::where('show_id', $show->id)
                ->whereIn('seat_code', $seatCodes)
                ->lockForUpdate()
                ->get();

            if ($seats->count() !== count($seatCodes)) {
                throw new RuntimeException('One or more selected seats do not exist for this show.');
            }

            $unavailable = $seats->filter(fn ($s) => ! $s->isBookable() && $s->locked_by !== $user->id);

            if ($unavailable->isNotEmpty()) {
                throw new RuntimeException('Sorry, seat(s) ' . $unavailable->pluck('seat_code')->implode(', ') . ' were just taken. Please pick different seats.');
            }

            foreach ($seats as $seat) {
                $seat->update([
                    'status' => 'locked',
                    'locked_by' => $user->id,
                    'locked_at' => now(),
                ]);
            }

            return $seats->all();
        });
    }

    /*
      Release seats a user locked but didn't complete payment for
      when they navigated away from the summary page
     */
    public function releaseSeats(Show $show, array $seatCodes, User $user): void
    {
        ShowSeat::where('show_id', $show->id)
            ->whereIn('seat_code', $seatCodes)
            ->where('locked_by', $user->id)
            ->where('status', 'locked')
            ->update(['status' => 'available', 'locked_by' => null, 'locked_at' => null]);
    }

    /*
      Confirm the booking: verify locked seats belong to this user, debit the
      wallet, mark seats booked, create Booking + BookingSeat + Payment rows.
     */
    public function createBooking(Show $show, array $seatCodes, User $user): Booking
    {
        return DB::transaction(function () use ($show, $seatCodes, $user) {
            $seats = ShowSeat::where('show_id', $show->id)
                ->whereIn('seat_code', $seatCodes)
                ->lockForUpdate()
                ->get();

            if ($seats->count() !== count($seatCodes)) {
                throw new RuntimeException('One or more selected seats are invalid.');
            }

            foreach ($seats as $seat) {
                $stillHeldByUser = $seat->status === 'locked' && $seat->locked_by === $user->id
                    && $seat->locked_at && $seat->locked_at->gt(now()->subMinutes(10));

                if (! $stillHeldByUser && ! $seat->isBookable()) {
                    throw new RuntimeException('Your seat hold expired or seat ' . $seat->seat_code . ' was taken. Please select seats again.');
                }
            }

            $totalAmount = $seats->sum('price');

            $booking = Booking::create([
                'booking_number' => 'MB' . now()->format('Ymd') . strtoupper(Str::random(6)),
                'booking_code' => strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'show_id' => $show->id,
                'seat_count' => $seats->count(),
                'total_amount' => $totalAmount,
                'payment_method' => 'wallet',
                'status' => 'confirmed',
            ]);

            // Debit wallet — throws if insufficient balance
            // the whole transaction (booking + seat status changes included).
            $this->walletService->debit(
                $user,
                (float) $totalAmount,
                $booking,
                'Ticket payment for booking ' . $booking->booking_number
            );

            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'amount' => $totalAmount,
                'method' => 'wallet',
                'status' => 'success',
                'reference' => 'PAY-' . strtoupper(Str::random(10)),
            ]);

            foreach ($seats as $seat) {
                BookingSeat::create([
                    'booking_id' => $booking->id,
                    'show_seat_id' => $seat->id,
                    'seat_code' => $seat->seat_code,
                    'seat_type' => $seat->seat_type,
                    'price' => $seat->price,
                ]);

                $seat->update(['status' => 'booked', 'locked_by' => null, 'locked_at' => null]);
            }

            $show->decrement('available_seats', $seats->count());

            return $booking->fresh(['seats', 'show.movie', 'show.cinema', 'show.hall']);
        });
    }

    /*
      Cancel a confirmed booking..see Booking::isCancellable()) and automatically refund  to wallet.
     */
    public function cancelBooking(Booking $booking, bool $force = false): Booking
    {
        if (! $force && ! $booking->isCancellable()) {
            throw new RuntimeException('This booking can no longer be cancelled (showtime is too close, or it is already cancelled/completed).');
        }

        if ($force && $booking->status !== 'confirmed') {
            throw new RuntimeException('This booking is not in a cancellable state.');
        }

        return DB::transaction(function () use ($booking) {
            $booking->update([
                'status' => 'cancelled',
                'refund_status' => 'refunded',
                'refund_amount' => $booking->total_amount,
                'cancelled_at' => now(),
            ]);

            $this->walletService->refund(
                $booking->user,
                (float) $booking->total_amount,
                $booking,
                'Refund for cancelled booking ' . $booking->booking_number
            );

            $seatIds = $booking->seats->pluck('show_seat_id');
            ShowSeat::whereIn('id', $seatIds)->update(['status' => 'available', 'locked_by' => null, 'locked_at' => null]);

            $booking->show->increment('available_seats', $booking->seat_count);

            if ($booking->payment) {
                $booking->payment->update(['status' => 'refunded']);
            }

            return $booking->fresh();
        });
    }
}
