<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class TMDbService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.themoviedb.org/3';
    protected string $imageBaseUrl = 'https://image.tmdb.org/t/p/original';

    public function __construct()
    {
        $this->apiKey = (string) config('services.tmdb.key');
    }

    protected function ensureConfigured(): void
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('TMDB_API_KEY is not set in .env');
        }
    }

 
     // Quick search — used for the admin type-ahead suggestions list.
     
    public function search(string $query): array
    {
        $this->ensureConfigured();

        $response = Http::get("{$this->baseUrl}/search/movie", [
            'api_key' => $this->apiKey,
            'query' => $query,
            'include_adult' => true,
            'language' => 'en-US',
            'page' => 1,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('TMDb search request failed: ' . $response->status());
        }

        return $response->json('results', []);
    }

  
     // Fetch full movie details for a given TMDb movie ID.
  
   
    public function fetchMovieDetails(int $tmdbId): array
    {
        $this->ensureConfigured();

        $response = Http::get("{$this->baseUrl}/movie/{$tmdbId}", [
            'api_key' => $this->apiKey,
            'language' => 'en-US',
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('TMDb movie details request failed: ' . $response->status());
        }

        $data = $response->json();

        return [
            'tmdb_id' => $data['id'],
            'title' => $data['title'] ?? $data['original_title'] ?? 'Untitled',
            'original_title' => $data['original_title'] ?? null,
            'original_language' => $data['original_language'] ?? null,
            'overview' => $data['overview'] ?? null,
            'poster_path' => $data['poster_path'] ?? null, // TMDb relative path, e.g. /abc.jpg
            'backdrop_path' => $data['backdrop_path'] ?? null,
            'release_date' => $data['release_date'] ?: null,
            'runtime' => $data['runtime'] ?? null,
            'language' => $data['original_language'] ?? null,
            'popularity' => $data['popularity'] ?? null,
            'vote_average' => $data['vote_average'] ?? null,
            'vote_count' => $data['vote_count'] ?? null,
            'adult' => $data['adult'] ?? false,
            'status' => $data['status'] ?? null,
            'production_countries' => isset($data['production_countries'])
                ? collect($data['production_countries'])->pluck('name')->implode(', ')
                : null,
            'genres' => collect($data['genres'] ?? [])->map(fn ($g) => [
                'tmdb_id' => $g['id'],
                'name' => $g['name'],
            ])->all(),
        ];
    }

   
    //  Download a TMDb image (poster or backdrop) and store it locally under..
    //  storage/app/public/posters..
    
     
    public function downloadImage(?string $tmdbRelativePath, string $folder = 'posters'): ?string
    {
        if (empty($tmdbRelativePath)) {
            return null;
        }

        $url = $this->imageBaseUrl . $tmdbRelativePath;

        $response = Http::timeout(20)->get($url);

        if (! $response->successful()) {
            return null;
        }

        $extension = pathinfo($tmdbRelativePath, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = $folder . '/' . Str::uuid() . '.' . $extension;

        Storage::disk('public')->put($filename, $response->body());

        return $filename; // relative path stored in DB
    }
}
