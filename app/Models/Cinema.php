<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cinema extends Model
{
    protected $fillable = [
        'name', 'slug', 'address', 'location', 'latitude', 'longitude',
        'phone', 'email', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::creating(function (Cinema $cinema) {
            if (empty($cinema->slug)) {
                $base = Str::slug($cinema->name);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $cinema->slug = $slug;
            }
        });
    }

    public function halls()
    {
        return $this->hasMany(Hall::class);
    }

    public function shows()
    {
        return $this->hasMany(Show::class);
    }
}
