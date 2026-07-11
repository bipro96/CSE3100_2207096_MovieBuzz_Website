<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MovieController extends Controller
{


  public function home()
    {
       $featured = Movie::featured()->latest()->take(6)->get();

       $nowShowing = Movie::nowShowing()->latest()->take(10)->get();

        $upcoming = Movie::upcoming()->latest()->take(10)->get();

        $genres = Genre::orderBy('name')->get();

        return view('movies.home', compact('featured', 'nowShowing', 'upcoming', 'genres'));
    }

    


public function index(Request $request)
{
    $genres = Genre::orderBy('name')->get();

    $movies = Movie::query()
        ->where('is_active', true)
        ->with('genres')
        ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%' . $request->q . '%'))
        ->when($request->filled('genre'), fn ($q) => $q->whereHas('genres', fn ($g) => $g->where('slug', $request->genre)))
        ->when($request->filled('language'), fn ($q) => $q->where('language', $request->language))
        ->when($request->filled('year'), fn ($q) => $q->whereYear('release_date', $request->year))
        ->when($request->filled('status'), fn ($q) => $q->where('listing_status', $request->status))
        ->orderByDesc('release_date')
        ->paginate(12)
        ->withQueryString();

    return view('movies.index', compact('movies', 'genres'));
}





    /**
     * AJAX instant search — used by the search bar's live dropdown.
     */
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:1']);

        $movies = Movie::where('is_active', true)
            ->where('title', 'like', '%' . $request->q . '%')
            ->limit(8)
            ->get(['id', 'title', 'slug', 'poster_path', 'release_date']);

        return response()->json([
            'results' => $movies->map(fn ($m) => [
                'title' => $m->title,
                'year' => $m->release_date?->format('Y'),
                'poster' => $m->posterUrl(),
                'url' => route('movies.show', $m->slug),
            ]),
        ]);
    }

    public function show(string $slug)
    {
        $movie = Movie::where('slug', $slug)
            ->with(['genres', 'approvedReviews.user'])
            ->firstOrFail();

        $shows = $movie->upcomingShows()
            ->with('cinema', 'hall')
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn ($show) => $show->starts_at->format('Y-m-d'))
            ->map(fn ($group) => $group->groupBy('cinema.name'));

        $isWishlisted = auth()->check()
            ? auth()->user()->wishlists()->where('movie_id', $movie->id)->exists()
            : false;

        return view('movies.show', compact('movie', 'shows', 'isWishlisted'));
    }

    public function toggleWishlist(Movie $movie)
    {
        $user = auth()->user();
        $existing = $user->wishlists()->where('movie_id', $movie->id)->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
        } else {
            $user->wishlists()->create(['movie_id' => $movie->id]);
            $status = 'added';
        }

        if (request()->wantsJson()) {
            return response()->json(['status' => $status]);
        }

        return back()->with('success', $status === 'added' ? 'Added to wishlist.' : 'Removed from wishlist.');
    }

    public function wishlist()
    {
        $movies = auth()->user()->wishlistMovies()->with('genres')->get();
        return view('movies.wishlist', compact('movies'));
    }
}
