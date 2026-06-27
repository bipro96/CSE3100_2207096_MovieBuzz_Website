@extends('layouts.admin')

@section('title', 'Movies')
@section('page-title', 'Manage Movies')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search movies...">
        <select name="status" class="form-select form-select-sm">
            <option value="">All Status</option>
            <option value="now_showing" @selected(request('status')==='now_showing')>Now Showing</option>
            <option value="upcoming" @selected(request('status')==='upcoming')>Upcoming</option>
            <option value="archived" @selected(request('status')==='archived')>Archived</option>
        </select>
        <button class="btn btn-sm btn-outline-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.movies.create') }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-plus"></i> Add Movie</a>
</div>

<div class="admin-panel-box p-3">
    <table class="table align-middle">
        <thead>
            <tr>
                <th>Poster</th>
                <th>Title</th>
                <th>Genres</th>
                <th>Release</th>
                <th>Status</th>
                <th>Active</th>
                <th>Featured</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($movies as $movie)
                <tr>
                    <td><img src="{{ $movie->posterUrl() }}" style="width:45px;height:65px;object-fit:cover;border-radius:4px"></td>
                    <td>{{ $movie->title }}</td>
                    <td class="small">{{ $movie->genres->pluck('name')->implode(', ') }}</td>
                    <td class="small">{{ $movie->release_date?->format('d M Y') }}</td>
                    <td><span class="badge bg-secondary">{{ str_replace('_',' ', $movie->listing_status) }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.movies.toggle-active', $movie) }}">
                            @csrf
                            <button class="btn btn-sm btn-{{ $movie->is_active ? 'success' : 'outline-secondary' }}">
                                {{ $movie->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.movies.toggle-featured', $movie) }}">
                            @csrf
                            <button class="btn btn-sm btn-{{ $movie->is_featured ? 'warning' : 'outline-secondary' }}">
                                <i class="fa-solid fa-star"></i>
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.movies.edit', $movie) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.movies.destroy', $movie) }}" class="d-inline" onsubmit="return confirm('Delete this movie?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-secondary py-4">No movies found.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $movies->links() }}
</div>

@endsection
