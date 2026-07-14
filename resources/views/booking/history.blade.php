@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">My Bookings</h3>

    <div class="mb-filter-box p-3">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Booking #</th>
                    <th>Movie</th>
                    <th>Cinema</th>
                    <th>Show Time</th>
                    <th>Seats</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Refund</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td class="small">{{ $booking->booking_number }}</td>
                        <td>{{ $booking->show->movie->title }}</td>
                        <td class="small">{{ $booking->show->cinema->name }}</td>
                        <td class="small">{{ $booking->show->starts_at->format('d M, h:i A') }}</td>
                        <td class="small">{{ $booking->seat_count }}</td>
                        <td>৳{{ number_format($booking->total_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="small">{{ $booking->refund_status !== 'none' ? ucfirst($booking->refund_status) : '-' }}</td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('booking.ticket', $booking) }}" class="btn btn-sm btn-outline-warning">Ticket</a>
                            @if($booking->isCancellable())
                                <form method="POST" action="{{ route('booking.cancel', $booking) }}" onsubmit="return confirm('Cancel this booking? Amount will be refunded to your wallet.')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-secondary py-4">You haven't booked any tickets yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $bookings->links() }}
    </div>
</div>
@endsection
