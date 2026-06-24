@extends('layouts.app')

@section('title', 'My Wishlist - MovieBuzz')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-heart text-danger"></i> My Wishlist</h3>
    <div class="row g-4">
        @forelse($movies as $movie)
            <div class="col-6 col-md-4 col-lg-2">
                @include('movies.partials.card', ['movie' => $movie])
            </div>
        @empty
            <p class="text-secondary">Your wishlist is empty. Browse movies and tap the heart icon to add some!</p>
        @endforelse
    </div>
</div>
@endsection
