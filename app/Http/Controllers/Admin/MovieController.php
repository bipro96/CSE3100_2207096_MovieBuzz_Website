<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Movie;
use App\Services\TMDbService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
    public function __construct(protected TMDbService $tmdb)
    {
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

    /**
     * AJAX: search TMDb by title, return a short list of matches for the admin to pick from.
     */
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

    /**
     * AJAX: "Fetch Movie" button — pull full details for a specific TMDb ID
     * and auto-fill the create/edit form. Does NOT save to DB yet.
     */
    public function tmdbFetch(Request $request)
    {
        $request->validate(['tmdb_id' => 'required|integer']);

        if (Movie::where('tmdb_id', $request->tmdb_id)->exists()) {
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

        DB::transaction(function () use ($validated, &$movie) {
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
                'adult' => (bool) ($validated['adult'] ?? false),
                'is_featured' => (bool) ($validated['is_featured'] ?? false),
                'is_active' => (bool) ($validated['is_active'] ?? true),
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

        $validated['is_featured'] = (bool) ($validated['is_featured'] ?? false);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $movie->update(collect($validated)->except(['genre_ids', 'poster', 'backdrop'])->all());
        $movie->genres()->sync($validated['genre_ids'] ?? []);

        return redirect()->route('admin.movies.index')->with('success', 'Movie updated successfully.');
    }

    public function destroy(Movie $movie)
    {
        $movie->delete();
        return back()->with('success', 'Movie deleted.');
    }

    public function toggleActive(Movie $movie)
    {
        $movie->update(['is_active' => ! $movie->is_active]);
        return back()->with('success', 'Movie status updated.');
    }

    public function toggleFeatured(Movie $movie)
    {
        $movie->update(['is_featured' => ! $movie->is_featured]);
        return back()->with('success', 'Featured status updated.');
    }
}
