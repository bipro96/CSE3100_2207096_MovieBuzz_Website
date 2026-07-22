<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Show;
use App\Services\BookingService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use RuntimeException;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected WalletService $walletService
    ) {
    }


     // Seat selection page for a given show.
     
    public function seats(Show $show)
    {
        if ($show->starts_at->isPast()) {
            abort(404);
        }

        $show->load('movie', 'cinema', 'hall.seats');
        $seats = $show->seats()->get();

        // Group seats into a grid by row for rendering.
        $seatsByRow = $seats->groupBy(fn ($s) => preg_replace('/[0-9]+/', '', $s->seat_code));

        return view('booking.seats', compact('show', 'seatsByRow'));
    }

    //AJAX: lock the selected seats for this user for 10 minutes and
     // move them to the summary step.
     
    public function lock(Request $request, Show $show)
    {
        $request->validate([
            'seats' => 'required|array|min:1|max:10',
            'seats.*' => 'string',
        ]);

        try {
            $this->bookingService->lockSeats($show, $request->seats, $request->user());
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'redirect' => route('booking.summary', ['show' => $show->id, 'seats' => implode(',', $request->seats)]),
        ]);
    }

   
     // Booking summary page - shows chosen seats, price breakdown, wallet balance.
 
    public function summary(Request $request, Show $show)
    {
        $seatCodes = array_filter(explode(',', $request->query('seats', '')));

        $seats = $show->seats()
            ->whereIn('seat_code', $seatCodes)
            ->where('locked_by', $request->user()->id)
            ->get();

        if ($seats->isEmpty()) {
            return redirect()->route('booking.seats', $show)->with('error', 'Your seat selection expired. Please select seats again.');
        }

        $wallet = $this->walletService->walletFor($request->user());
        $total = $seats->sum('price');

        $show->load('movie', 'cinema', 'hall');

        return view('booking.summary', compact('show', 'seats', 'wallet', 'total', 'seatCodes'));
    }

     // Confirm & pay with wallet.
     
    public function confirm(Request $request, Show $show)
    {
        $request->validate([
            'seats' => 'required|string',
        ]);

        $seatCodes = array_filter(explode(',', $request->seats));

        try {
            $booking = $this->bookingService->createBooking($show, $seatCodes, $request->user());
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('booking.success', $booking)->with('success', 'Booking confirmed! Enjoy the show.');
    }

  
     // Release seats if the user backs out of the summary page.
     
    public function cancelHold(Request $request, Show $show)
    {
        $seatCodes = array_filter(explode(',', $request->input('seats', '')));
        $this->bookingService->releaseSeats($show, $seatCodes, $request->user());

        return redirect()->route('movies.show', $show->movie->slug);
    }

    public function success(Booking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);
        $booking->load('show.movie', 'show.cinema', 'show.hall', 'seats');

        // QR payload — rendered client-side via a QR image service, no extra package needed.
        $qrPayload = json_encode([
            'booking_id' => $booking->booking_number,
            'code' => $booking->booking_code,
            'movie' => $booking->show->movie->title,
            'cinema' => $booking->show->cinema->name,
            'hall' => $booking->show->hall->name,
            'seats' => $booking->seats->pluck('seat_code'),
            'date' => $booking->show->starts_at->format('Y-m-d'),
            'time' => $booking->show->starts_at->format('H:i'),
        ]);

        return view('booking.success', compact('booking', 'qrPayload'));
    }

    public function history()
    {
        $bookings = auth()->user()->bookings()
            ->with('show.movie', 'show.cinema')
            ->latest()
            ->paginate(10);

        return view('booking.history', compact('bookings'));
    }

    public function ticket(Booking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);
        $booking->load('show.movie', 'show.cinema', 'show.hall', 'seats');

        $qrPayload = json_encode([
            'booking_id' => $booking->booking_number,
            'code' => $booking->booking_code,
            'movie' => $booking->show->movie->title,
            'seats' => $booking->seats->pluck('seat_code'),
        ]);

        return view('booking.ticket', compact('booking', 'qrPayload'));
    }

    public function cancel(Booking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        try {
            $this->bookingService->cancelBooking($booking);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Booking cancelled and ৳' . number_format($booking->total_amount, 2) . ' refunded to your wallet.');
    }
}
