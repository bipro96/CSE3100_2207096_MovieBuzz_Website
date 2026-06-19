<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Movie extends Model
{
    protected $fillable = [
        'tmdb_id', 'title', 'slug', 'original_title', 'original_language',
        'overview', 'poster_path', 'backdrop_path', 'release_date', 'runtime',
        'language', 'popularity', 'vote_average', 'vote_count', 'adult',
        'status', 'production_countries', 'rating_avg', 'rating_count',
        'listing_status', 'is_featured', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'adult' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'popularity' => 'decimal:3',
            'vote_average' => 'decimal:2',
            'rating_avg' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Movie $movie) {
            if (empty($movie->slug)) {
                $base = Str::slug($movie->title);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $movie->slug = $slug;
            }
        });
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_movie');
    }


    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    public function recalculateRating(): void
    {
        $approved = $this->approvedReviews();
        $this->update([
            'rating_avg' => round($approved->avg('rating') ?? 0, 2),
            'rating_count' => $approved->count(),
        ]);
    }
}
