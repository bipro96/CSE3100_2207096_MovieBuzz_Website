<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $review = Review::updateOrCreate(
            ['user_id' => $request->user()->id, 'movie_id' => $movie->id],
            ['rating' => $validated['rating'], 'comment' => $validated['comment'] ?? null, 'status' => 'pending']
        );

        return back()->with('success', 'Thanks for your review! It will appear once approved by our team.');
    }

    public function update(Request $request, Review $review)
    {
        abort_unless($review->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $review->update([...$validated, 'status' => 'pending']);
        $review->movie->recalculateRating();

        return back()->with('success', 'Review updated.');
    }

    public function destroy(Request $request, Review $review)
    {
        abort_unless($review->user_id === $request->user()->id, 403);

        $movie = $review->movie;
        $review->delete();
        $movie->recalculateRating();

        return back()->with('success', 'Review deleted.');
    }
}
