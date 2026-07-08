<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with('user', 'movie')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        $review->movie->recalculateRating();

        return back()->with('success', 'Review approved.');
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        $review->movie->recalculateRating();

        return back()->with('success', 'Review rejected.');
    }

    public function destroy(Review $review)
    {
        $movie = $review->movie;
        $review->delete();
        $movie->recalculateRating();

        return back()->with('success', 'Review deleted.');
    }
}
