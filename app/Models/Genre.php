<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = ['tmdb_id', 'name', 'slug'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'genre_movie');
    }
}
