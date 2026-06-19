<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSeat extends Model
{
    protected $fillable = ['booking_id', 'show_seat_id', 'seat_code', 'seat_type', 'price'];


    public function showSeat()
    {
        return $this->belongsTo(ShowSeat::class);
    }
}
