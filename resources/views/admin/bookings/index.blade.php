@extends('layouts.admin')

@section('title', 'Bookings')
@section('page-title', 'Manage Bookings')

@section('content')

<form method="GET" class="mb-3 d-flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search booking number...">
    <select name="status" class="form-select form-select-sm">
        <option value="">All Status</option>
        <option value="confirmed" @selected(request('status')==='confirmed')>Confirmed</option>
        <option value="cancelled" @selected(request('status')==='cancelled')>Cancelled</option>
        <option value="completed" @selected(request('status')==='completed')>Completed</option>
    </select>
    <button class="btn btn-sm btn-outline-secondary">Filter</button>
</form>

<div class="admin-panel-box p-3">
    <table class="table align-middle">
        <thead>
            <tr><th>Booking #</th><th>User</th><th>Movie</th><th>Show Time</th><th>Seats</th><th>Amount</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td class="small">{{ $booking->booking_number }}</td>
                    <td class="small">{{ $booking->user->name }}</td>
                    <td>{{ $booking->show->movie->title ?? 'N/A' }}</td>
                    <td class="small">{{ $booking->show->starts_at->format('d M, h:i A') }}</td>
                    <td>{{ $booking->seat_count }}</td>
                    <td>৳{{ number_format($booking->total_amount, 2) }}</td>
                    <td><span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'secondary') }}">{{ ucfirst($booking->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a>
                        @if($booking->status === 'confirmed')
                            <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" class="d-inline" onsubmit="return confirm('Cancel and refund this booking?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-ban"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-secondary py-4">No bookings yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $bookings->links() }}
</div>
@endsection
