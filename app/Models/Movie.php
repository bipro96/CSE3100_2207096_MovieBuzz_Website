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

    public function shows()
    {
        return $this->hasMany(Show::class);
    }

    public function upcomingShows()
    {
        return $this->hasMany(Show::class)->where('starts_at', '>', now())->where('status', 'scheduled');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved')->latest();
    }

    public function posterUrl(): string
    {
        if ($this->poster_path && Storage::disk('public')->exists($this->poster_path)) {
            return Storage::url($this->poster_path);
        }
        return asset('images/poster-placeholder.png');
    }

    public function backdropUrl(): string
    {
        if ($this->backdrop_path && Storage::disk('public')->exists($this->backdrop_path)) {
            return Storage::url($this->backdrop_path);
        }
        return asset('images/backdrop-placeholder.png');
    }

    public function scopeNowShowing($query)
    {
        return $query->where('listing_status', 'now_showing')->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('listing_status', 'upcoming')->where('is_active', true);
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
