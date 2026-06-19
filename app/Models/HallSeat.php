<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HallSeat extends Model
{
    protected $fillable = ['hall_id', 'row_label', 'column_number', 'seat_code', 'seat_type'];

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }
}
