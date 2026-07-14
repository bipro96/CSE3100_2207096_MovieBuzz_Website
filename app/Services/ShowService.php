<?php

namespace App\Services;

use App\Models\Hall;
use App\Models\Show;
use App\Models\ShowSeat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ShowService
{
    public function hasOverlap(int $hallId, Carbon $startsAt, Carbon $endsAt, ?int $ignoreShowId = null): bool
    {
        return Show::where('hall_id', $hallId)
            ->where('status', '!=', 'cancelled')
            ->when($ignoreShowId, fn ($q) => $q->where('id', '!=', $ignoreShowId))
            ->where(function ($q) use ($startsAt, $endsAt) {
                $q->whereBetween('starts_at', [$startsAt, $endsAt->copy()->subMinute()])
                    ->orWhereBetween('ends_at', [$startsAt->copy()->addMinute(), $endsAt])
                    ->orWhere(function ($q2) use ($startsAt, $endsAt) {
                        $q2->where('starts_at', '<=', $startsAt)->where('ends_at', '>=', $endsAt);
                    });
            })
            ->exists();
    }

  
    public function createShow(array $data): Show
    {
        $hall = Hall::with('seats')->findOrFail($data['hall_id']);

        $startsAt = Carbon::parse($data['show_date'] . ' ' . $data['show_time']);
        $movie = \App\Models\Movie::findOrFail($data['movie_id']);
        $runtime = $movie->runtime ?: 120;
        $endsAt = $startsAt->copy()->addMinutes($runtime + 20); // +20 min buffer for cleaning/ads

        if ($this->hasOverlap($hall->id, $startsAt, $endsAt)) {
            throw new RuntimeException('This hall already has a show scheduled that overlaps with this time slot.');
        }

        return DB::transaction(function () use ($data, $hall, $startsAt, $endsAt) {
            $show = Show::create([
                'movie_id' => $data['movie_id'],
                'cinema_id' => $hall->cinema_id,
                'hall_id' => $hall->id,
                'show_date' => $startsAt->toDateString(),
                'show_time' => $startsAt->format('H:i:s'),
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'language' => $data['language'] ?? null,
                'format' => $data['format'] ?? '2D',
                'ticket_price' => $data['ticket_price'],
                'premium_price' => $data['premium_price'] ?? null,
                'vip_price' => $data['vip_price'] ?? null,
                'total_seats' => 0,
                'available_seats' => 0,
                'status' => 'scheduled',
            ]);

            $bookableSeats = $hall->seats->reject(fn ($s) => $s->seat_type === 'unavailable');

            foreach ($bookableSeats as $seat) {
                ShowSeat::create([
                    'show_id' => $show->id,
                    'hall_seat_id' => $seat->id,
                    'seat_code' => $seat->seat_code,
                    'seat_type' => $seat->seat_type,
                    'price' => $show->priceFor($seat->seat_type),
                    'status' => 'available',
                ]);
            }

            $show->update([
                'total_seats' => $bookableSeats->count(),
                'available_seats' => $bookableSeats->count(),
            ]);

            return $show;
        });
    }

    public function cancelShow(Show $show): void
    {
        $show->update(['status' => 'cancelled']);
        // Any confirmed bookings for a cancelled show should be refunded by
        // an admin action (see Admin\ShowController::cancel) which loops
        // bookings through BookingService::cancel for a full audit
    }
}
