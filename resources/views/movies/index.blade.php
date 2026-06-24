
@extends('layouts.app')

@section('title', 'Browse Movies - MovieBuzz')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <form method="GET" action="{{ route('movies.index') }}" class="mb-filter-box p-3">
                <h6 class="fw-bold mb-3">Filters</h6>

                <div class="mb-3">
                    <label class="form-label small">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Movie title...">
                </div>

                <div class="mb-3">
                    <label class="form-label small">Genre</label>
                    <select name="genre" class="form-select">
                        <option value="">All Genres</option>
                        @foreach($genres as $genre)
                            <option value="{{ $genre->slug }}" @selected(request('genre') === $genre->slug)>{{ $genre->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="now_showing" @selected(request('status') === 'now_showing')>Now Showing</option>
                        <option value="upcoming" @selected(request('status') === 'upcoming')>Upcoming</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small">Release Year</label>
                    <input type="number" name="year" value="{{ request('year') }}" class="form-control" placeholder="2026" min="1950" max="2100">
                </div>

                <button class="btn btn-warning w-100">Apply Filters</button>
                <a href="{{ route('movies.index') }}" class="btn btn-outline-secondary w-100 mt-2">Reset</a>
            </form>
        </div>

        <div class="col-lg-9">
            <div class="row g-4">
                @forelse($movies as $movie)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('movies.partials.card', ['movie' => $movie])
                    </div>
                @empty
                    <p class="text-secondary">No movies matched your search.</p>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $movies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
