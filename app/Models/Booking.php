<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_number', 'booking_code', 'user_id', 'show_id', 'seat_count',
        'total_amount', 'payment_method', 'status', 'refund_status',
        'refund_amount', 'cancelled_at',
    ];

   

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

  
}
