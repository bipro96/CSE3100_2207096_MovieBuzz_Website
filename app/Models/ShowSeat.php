<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowSeat extends Model
{
    protected $fillable = [
        'show_id', 'hall_seat_id', 'seat_code', 'seat_type', 'price',
        'status', 'locked_by', 'locked_at',
    ];

  
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

}
