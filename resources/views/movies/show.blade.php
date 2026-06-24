@extends('layouts.app')

@section('title', $movie->title . ' - MovieBuzz')

@section('content')

<div class="mb-backdrop" style="background-image: linear-gradient(180deg, rgba(10,10,20,.4), rgba(10,10,20,.95)), url('{{ $movie->backdropUrl() }}')">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-3">
                <img src="{{ $movie->posterUrl() }}" class="img-fluid rounded shadow" alt="{{ $movie->title }}">
            </div>
            <div class="col-md-9 text-white">
                <h1 class="fw-bold">{{ $movie->title }}</h1>
                <div class="mb-3">
                    @foreach($movie->genres as $genre)
                        <span class="badge bg-secondary me-1">{{ $genre->name }}</span>
                    @endforeach
                </div>
                <p class="opacity-75 mb-3" style="max-width: 700px">{{ $movie->overview }}</p>
                <ul class="list-inline small opacity-75">
                    @if($movie->release_date)<li class="list-inline-item"><i class="fa-regular fa-calendar"></i> {{ $movie->release_date->format('d M, Y') }}</li>@endif
                    @if($movie->runtime)<li class="list-inline-item ms-3"><i class="fa-regular fa-clock"></i> {{ $movie->runtime }} min</li>@endif
                    @if($movie->language)<li class="list-inline-item ms-3"><i class="fa-solid fa-language"></i> {{ strtoupper($movie->language) }}</li>@endif
                    @if($movie->rating_count)<li class="list-inline-item ms-3"><i class="fa-solid fa-star text-warning"></i> {{ number_format($movie->rating_avg, 1) }} ({{ $movie->rating_count }} reviews)</li>@endif
                </ul>

                @auth
                    <form method="POST" action="{{ route('movies.wishlist.toggle', $movie) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-outline-light btn-sm">
                            <i class="fa-{{ $isWishlisted ? 'solid' : 'regular' }} fa-heart"></i>
                            {{ $isWishlisted ? 'In Wishlist' : 'Add to Wishlist' }}
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <h4 class="fw-bold mb-4">Showtimes</h4>

    @forelse($shows as $date => $cinemas)
        <div class="mb-4">
            <h6 class="text-warning fw-bold">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</h6>
            @foreach($cinemas as $cinemaName => $showsForCinema)
                <div class="mb-showtime-block p-3 mb-2">
                    <p class="fw-semibold mb-2">{{ $cinemaName }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($showsForCinema as $show)
                            <a href="{{ route('booking.seats', $show) }}" class="btn btn-outline-warning btn-sm">
                                {{ $show->starts_at->format('h:i A') }} &middot; {{ $show->format }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <p class="text-secondary">No upcoming showtimes scheduled yet. Please check back soon.</p>
    @endforelse

    <h4 class="fw-bold mt-5 mb-4">Reviews</h4>
    @forelse($movie->approvedReviews as $review)
        <div class="border-bottom pb-3 mb-3">
            <div class="d-flex justify-content-between">
                <strong>{{ $review->user->name }}</strong>
                <span class="text-warning">
                    @for($i = 0; $i < 5; $i++)
                        <i class="fa-{{ $i < $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                    @endfor
                </span>
            </div>
            <p class="mb-0 text-secondary small">{{ $review->comment }}</p>
        </div>
    @empty
        <p class="text-secondary">No reviews yet. Be the first to review this movie after watching it!</p>
    @endforelse

    @auth
        <div class="mb-filter-box p-3 mt-4" style="max-width:500px">
            <h6 class="fw-bold mb-3">Write a Review</h6>
            <form method="POST" action="{{ route('reviews.store', $movie) }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label small">Rating</label>
                    <select name="rating" class="form-select form-select-sm" required>
                        <option value="">Select rating</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}">{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="mb-2">
                    <textarea name="comment" rows="3" class="form-control form-control-sm" placeholder="Share your thoughts (optional)"></textarea>
                </div>
                <button class="btn btn-warning btn-sm">Submit Review</button>
            </form>
        </div>
    @endauth
</div>

@endsection
