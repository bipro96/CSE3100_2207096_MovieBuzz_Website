<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Genre;
use App\Models\Movie;
use App\Services\TMDbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MovieController extends Controller
{
    public function __construct(protected TMDbService $tmdb)
    {
    }

    protected function clearListingCache(): void
    {
        foreach (['home.featured', 'home.now_showing', 'home.upcoming', 'genres.all'] as $key) {
            Cache::store('file')->forget($key);
        }
    }

    public function index(Request $request)
    {
        $movies = Movie::query()
            ->with('genres')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%' . $request->q . '%'))
            ->when($request->filled('status'), fn ($q) => $q->where('listing_status', $request->status))
            ->when($request->filled('active'), fn ($q) => $q->where('is_active', $request->active === '1'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.movies.index', compact('movies'));
    }

    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        return view('admin.movies.create', compact('genres'));
    }

    
    public function tmdbSearch(Request $request)
    {
        $request->validate(['query' => 'required|string|min:2']);

        try {
            $results = $this->tmdb->search($request->query('query'));
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $mapped = collect($results)->take(8)->map(fn ($r) => [
            'tmdb_id' => $r['id'],
            'title' => $r['title'] ?? $r['original_title'] ?? 'Untitled',
            'release_date' => $r['release_date'] ?? null,
            'poster_path' => $r['poster_path'] ?? null,
            'poster_thumb' => $r['poster_path'] ? 'https://image.tmdb.org/t/p/w92' . $r['poster_path'] : null,
        ])->values();

        return response()->json(['results' => $mapped]);
    }

   
    public function tmdbFetch(Request $request)
    {
        $request->validate([
            'tmdb_id' => 'required|integer',
            'movie_id' => 'nullable|exists:movies,id', 
        ]);
    //Checks if the movie already exits or not..
        $duplicateQuery = Movie::where('tmdb_id', $request->tmdb_id);
        if ($request->filled('movie_id')) {
            $duplicateQuery->where('id', '!=', $request->movie_id);
        }

        if ($duplicateQuery->exists()) {
            return response()->json(['message' => 'This movie has already been added.'], 422);
        }

        try {
            $details = $this->tmdb->fetchMovieDetails((int) $request->tmdb_id);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $details['poster_preview'] = $details['poster_path']
            ? 'https://image.tmdb.org/t/p/w342' . $details['poster_path']
            : null;
        $details['backdrop_preview'] = $details['backdrop_path']
            ? 'https://image.tmdb.org/t/p/w780' . $details['backdrop_path']
            : null;

        return response()->json($details);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tmdb_id' => 'nullable|integer|unique:movies,tmdb_id',
            'title' => 'required|string|max:255',
            'original_title' => 'nullable|string|max:255',
            'original_language' => 'nullable|string|max:10',
            'overview' => 'nullable|string',
            'poster_path' => 'nullable|string', // TMDb relative path from hidden field
            'backdrop_path' => 'nullable|string',
            'trailer_key' => 'nullable|string|max:50',
            'cast_json' => 'nullable|string', // JSON string from hidden field, decoded below
            'release_date' => 'nullable|date',
            'runtime' => 'nullable|integer|min:0',
            'language' => 'nullable|string|max:50',
            'popularity' => 'nullable|numeric',
            'vote_average' => 'nullable|numeric|min:0|max:10',
            'vote_count' => 'nullable|integer|min:0',
            'adult' => 'nullable|boolean',
            'status' => 'nullable|string|max:50',
            'production_countries' => 'nullable|string',
            'listing_status' => ['required', Rule::in(['now_showing', 'upcoming', 'archived'])],
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'genre_ids' => 'nullable|array',
            'genre_ids.*' => 'exists:genres,id',
            'genre_names' => 'nullable|array', // for genres pulled from TMDb not yet in our DB
            'genre_names.*' => 'string',
        ]);

        DB::transaction(function () use ($request, $validated, &$movie) {
            $posterPath = $validated['poster_path'] ?? null;
            $backdropPath = $validated['backdrop_path'] ?? null;

            // If the path still looks like a raw TMDb path (starts with "/"), download it locally.
            if ($posterPath && str_starts_with($posterPath, '/')) {
                $posterPath = $this->tmdb->downloadImage($posterPath, 'posters');
            }
            if ($backdropPath && str_starts_with($backdropPath, '/')) {
                $backdropPath = $this->tmdb->downloadImage($backdropPath, 'backdrops');
            }


            $movie = Movie::create([
                ...$validated,
                'poster_path' => $posterPath,
                'backdrop_path' => $backdropPath,
                'cast_json' => $castData,
                'adult' => $request->boolean('adult'),
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active'),
            ]);

            $genreIds = $validated['genre_ids'] ?? [];

            foreach (($validated['genre_names'] ?? []) as $name) {
                $genre = Genre::firstOrCreate(
                    ['name' => $name],
                    ['slug' => \Illuminate\Support\Str::slug($name)]
                );
                $genreIds[] = $genre->id;
            }

            $movie->genres()->sync(array_unique($genreIds));
        });

        $this->clearListingCache();

        return redirect()->route('admin.movies.index')->with('success', 'Movie added successfully.');
    }

    public function edit(Movie $movie)
    {
        $genres = Genre::orderBy('name')->get();
        $movie->load('genres');
        return view('admin.movies.edit', compact('movie', 'genres'));
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'overview' => 'nullable|string',
            'release_date' => 'nullable|date',
            'runtime' => 'nullable|integer|min:0',
            'language' => 'nullable|string|max:50',
            'trailer_key' => 'nullable|string|max:50',
            'cast_json' => 'nullable|string', // JSON string, only present after "Refresh from TMDb"
            'listing_status' => ['required', Rule::in(['now_showing', 'upcoming', 'archived'])],
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'genre_ids' => 'nullable|array',
            'genre_ids.*' => 'exists:genres,id',
            'poster' => 'nullable|image|max:4096', // optional manual replacement upload
            'backdrop' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster_path'] = $request->file('poster')->store('posters', 'public');
        }
        if ($request->hasFile('backdrop')) {
            $validated['backdrop_path'] = $request->file('backdrop')->store('backdrops', 'public');
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');

        // Cast only changes when the admin clicked "Refresh from TMDb"; otherwise
        
        if (! empty($validated['cast_json'])) {
            $decoded = json_decode($validated['cast_json'], true);
            $validated['cast_json'] = is_array($decoded) ? $decoded : null;
        } else {
            unset($validated['cast_json']);
        }

        $movie->update(collect($validated)->except(['genre_ids', 'poster', 'backdrop'])->all());
        $movie->genres()->sync($validated['genre_ids'] ?? []);

        $this->clearListingCache();

        return redirect()->route('admin.movies.index')->with('success', 'Movie updated successfully.');
    }

    public function destroy(Movie $movie)
    {
        $hasBookings = Booking::whereIn('show_id', $movie->shows()->pluck('id'))->exists();

        if ($hasBookings) {
            return back()->with('error', 'Cannot delete "' . $movie->title . '" - it has existing bookings tied to its shows. Use the "Active" toggle to hide it from customers instead, so booking history stays intact.');
        }

        $movie->delete(); // shows/show_seats for this movie cascade-delete automatically (no bookings exist to block it)
        $this->clearListingCache();
        return back()->with('success', 'Movie deleted.');
    }

    public function toggleActive(Movie $movie)
    {
        $movie->update(['is_active' => ! $movie->is_active]);
        $this->clearListingCache();
        return back()->with('success', 'Movie status updated.');
    }

    public function toggleFeatured(Movie $movie)
    {
        $movie->update(['is_featured' => ! $movie->is_featured]);
        $this->clearListingCache();
        return back()->with('success', 'Featured status updated.');
    }
}
