<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSeat extends Model
{
    protected $fillable = ['booking_id', 'show_seat_id', 'seat_code', 'seat_type', 'price'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function showSeat()
    {
        return $this->belongsTo(ShowSeat::class);
    }
}
