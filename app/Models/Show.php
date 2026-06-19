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


    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now())->where('status', 'scheduled');
    }
}
