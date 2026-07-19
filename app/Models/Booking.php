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

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function seats()
    {
        return $this->hasMany(BookingSeat::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

     // Cancellation is allowed up to 2 hours before showtime.
   
    public function isCancellable(): bool
    {
        return $this->status === 'confirmed'
            && $this->show
            && now()->lt($this->show->starts_at->subHours(2));
    }
}
