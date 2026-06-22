<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'locked_at' => 'datetime',
        ];
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function hallSeat()
    {
        return $this->belongsTo(HallSeat::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isBookable(): bool
    {
        if ($this->status === 'available') {
            return true;
        }
        // stale lock older than 10 minutes is considered free again
        if ($this->status === 'locked' && $this->locked_at && $this->locked_at->lt(now()->subMinutes(10))) {
            return true;
        }
        return false;
    }
}
