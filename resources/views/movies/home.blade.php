@extends('layouts.app')

@section('title', 'MovieBuzz - Book Movie Tickets Online')

@section('content')

<section class="mb-hero">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold text-white">Book Your Movie Tickets <span class="text-warning">in Seconds</span></h1>
                <p class="text-light opacity-75 mb-4">Discover the latest blockbusters, pick your favourite seats, and pay instantly with your MovieBuzz wallet.</p>
                <a href="{{ route('movies.index') }}" class="btn btn-warning btn-lg px-4"><i class="fa-solid fa-ticket"></i> Browse Movies</a>
            </div>
        </div>
    </div>
</section>

@if($featured->count())
<section class="container py-5">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-fire text-warning"></i> Featured</h3>
    <div class="row g-4">
        @foreach($featured as $movie)
            <div class="col-6 col-md-4 col-lg-2">
                @include('movies.partials.card', ['movie' => $movie])
            </div>
        @endforeach
    </div>
</section>
@endif

<section class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Now Showing</h3>
        <a href="{{ route('movies.index', ['status' => 'now_showing']) }}" class="small">View all <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="row g-4">
        @forelse($nowShowing as $movie)
            <div class="col-6 col-md-4 col-lg-2">
                @include('movies.partials.card', ['movie' => $movie])
            </div>
        @empty
            <p class="text-secondary">No movies currently showing.</p>
        @endforelse
    </div>
</section>

<section class="container py-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Upcoming</h3>
        <a href="{{ route('movies.index', ['status' => 'upcoming']) }}" class="small">View all <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="row g-4">
        @forelse($upcoming as $movie)
            <div class="col-6 col-md-4 col-lg-2">
                @include('movies.partials.card', ['movie' => $movie])
            </div>
        @empty
            <p class="text-secondary">No upcoming movies yet.</p>
        @endforelse
    </div>
</section>

@endsection
