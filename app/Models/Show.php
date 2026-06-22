<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    protected $fillable = [
        'movie_id', 'cinema_id', 'hall_id', 'show_date', 'show_time',
        'starts_at', 'ends_at', 'language', 'format', 'ticket_price',
        'premium_price', 'vip_price', 'total_seats', 'available_seats', 'status',
    ];

    protected function casts(): array
    {
        return [
            'show_date' => 'date',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'ticket_price' => 'decimal:2',
            'premium_price' => 'decimal:2',
            'vip_price' => 'decimal:2',
        ];
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function seats()
    {
        return $this->hasMany(ShowSeat::class)->orderBy('seat_code');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function priceFor(string $seatType): float
    {
        return match ($seatType) {
            'premium' => (float) ($this->premium_price ?? $this->ticket_price),
            'vip' => (float) ($this->vip_price ?? $this->ticket_price),
            default => (float) $this->ticket_price,
        };
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now())->where('status', 'scheduled');
    }
}
