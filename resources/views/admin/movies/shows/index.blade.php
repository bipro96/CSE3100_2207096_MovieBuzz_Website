@extends('layouts.admin')

@section('title', 'Shows')
@section('page-title', 'Manage Shows')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" class="d-flex gap-2">
        <select name="movie_id" class="form-select form-select-sm">
            <option value="">All Movies</option>
            @foreach($movies as $movie)
                <option value="{{ $movie->id }}" @selected(request('movie_id')==$movie->id)>{{ $movie->title }}</option>
            @endforeach
        </select>
        <select name="cinema_id" class="form-select form-select-sm">
            <option value="">All Cinemas</option>
            @foreach($cinemas as $cinema)
                <option value="{{ $cinema->id }}" @selected(request('cinema_id')==$cinema->id)>{{ $cinema->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-sm btn-outline-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.shows.create') }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-plus"></i> Schedule Show</a>
</div>

<div class="admin-panel-box p-3">
    <table class="table align-middle">
        <thead>
            <tr><th>Movie</th><th>Cinema / Hall</th><th>Date &amp; Time</th><th>Format</th><th>Price</th><th>Seats</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($shows as $show)
                <tr>
                    <td>{{ $show->movie->title }}</td>
                    <td class="small">{{ $show->cinema->name }} - {{ $show->hall->name }}</td>
                    <td class="small">{{ $show->starts_at->format('d M Y, h:i A') }}</td>
                    <td>{{ $show->format }}</td>
                    <td>৳{{ number_format($show->ticket_price, 2) }}</td>
                    <td>{{ $show->available_seats }}/{{ $show->total_seats }}</td>
                    <td>
                        <span class="badge bg-{{ $show->status === 'cancelled' ? 'danger' : ($show->status === 'completed' ? 'secondary' : 'success') }}">
                            {{ ucfirst($show->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.shows.edit', $show) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                        @if($show->status !== 'cancelled')
                            <form method="POST" action="{{ route('admin.shows.destroy', $show) }}" class="d-inline" onsubmit="return confirm('Cancel this show? All confirmed bookings will be refunded automatically.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-ban"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-secondary py-4">No shows scheduled yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $shows->links() }}
</div>
@endsection
