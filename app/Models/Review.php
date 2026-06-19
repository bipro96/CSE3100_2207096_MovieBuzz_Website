<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'movie_id', 'rating', 'comment', 'status'];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
