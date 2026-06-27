@extends('layouts.admin')

@section('title', 'Edit Movie')
@section('page-title', 'Edit Movie')

@section('content')
<div class="admin-panel-box p-4">
    <form method="POST" action="{{ route('admin.movies.update', $movie) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $movie->title) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Release Date</label>
                <input type="date" name="release_date" class="form-control" value="{{ old('release_date', $movie->release_date?->format('Y-m-d')) }}">
            </div>

            <div class="col-12">
                <label class="form-label">Overview</label>
                <textarea name="overview" rows="4" class="form-control">{{ old('overview', $movie->overview) }}</textarea>
            </div>

            <div class="col-md-4">
                <label class="form-label">Runtime (min)</label>
                <input type="number" name="runtime" class="form-control" value="{{ old('runtime', $movie->runtime) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Language</label>
                <input type="text" name="language" class="form-control" value="{{ old('language', $movie->language) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Listing Status</label>
                <select name="listing_status" class="form-select">
                    <option value="upcoming" @selected($movie->listing_status==='upcoming')>Upcoming</option>
                    <option value="now_showing" @selected($movie->listing_status==='now_showing')>Now Showing</option>
                    <option value="archived" @selected($movie->listing_status==='archived')>Archived</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Replace Poster</label>
                <input type="file" name="poster" class="form-control" accept="image/*">
                <img src="{{ $movie->posterUrl() }}" class="mt-2 border" style="width:120px;height:180px;object-fit:cover;border-radius:6px">
            </div>
            <div class="col-md-6">
                <label class="form-label">Replace Backdrop</label>
                <input type="file" name="backdrop" class="form-control" accept="image/*">
                <img src="{{ $movie->backdropUrl() }}" class="mt-2 border" style="width:100%;max-width:260px;height:100px;object-fit:cover;border-radius:6px">
            </div>

            <div class="col-12 d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isFeatured" @checked($movie->is_featured)>
                    <label class="form-check-label" for="isFeatured">Featured</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked($movie->is_active)>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Genres</label>
                <div class="d-flex flex-wrap gap-3">
                    @foreach($genres as $genre)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="genre_ids[]" value="{{ $genre->id }}"
                                id="genre_{{ $genre->id }}" @checked($movie->genres->contains($genre->id))>
                            <label class="form-check-label" for="genre_{{ $genre->id }}">{{ $genre->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-warning px-4">Update Movie</button>
            <a href="{{ route('admin.movies.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
        </div>
    </form>
</div>
@endsection
