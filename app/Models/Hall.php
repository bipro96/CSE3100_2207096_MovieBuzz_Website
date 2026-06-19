<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hall extends Model
{
    protected $fillable = ['cinema_id', 'name', 'rows', 'columns', 'capacity', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function cinema()
    {
        return $this->belongsTo(Cinema::class);
    }

    public function seats()
    {
        return $this->hasMany(HallSeat::class)->orderBy('row_label')->orderBy('column_number');
    }

    public function shows()
    {
        return $this->hasMany(Show::class);
    }
}
