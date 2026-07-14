@extends('layouts.admin')

@section('title', 'Booking Detail')
@section('page-title', 'Booking ' . $booking->booking_number)

@section('content')

<div class="admin-panel-box p-4" style="max-width:600px">
    <p><strong>Customer:</strong> {{ $booking->user->name }} ({{ $booking->user->email }})</p>
    <p><strong>Movie:</strong> {{ $booking->show->movie->title ?? 'N/A' }}</p>
    <p><strong>Cinema / Hall:</strong> {{ $booking->show->cinema->name }} - {{ $booking->show->hall->name }}</p>
    <p><strong>Show Time:</strong> {{ $booking->show->starts_at->format('d M Y, h:i A') }}</p>
    <p><strong>Seats:</strong> {{ $booking->seats->pluck('seat_code')->implode(', ') }}</p>
    <p><strong>Amount:</strong> ৳{{ number_format($booking->total_amount, 2) }}</p>
    <p><strong>Status:</strong> <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'secondary') }}">{{ ucfirst($booking->status) }}</span></p>
    <p><strong>Refund Status:</strong> {{ ucfirst($booking->refund_status) }}</p>
    @if($booking->payment)
        <p><strong>Payment Reference:</strong> {{ $booking->payment->reference }}</p>
    @endif

    @if($booking->status === 'confirmed')
        <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" onsubmit="return confirm('Cancel and refund this booking?')">
            @csrf
            <button class="btn btn-outline-danger btn-sm">Cancel & Refund</button>
        </form>
    @endif

    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm mt-3">Back to Bookings</a>
</div>
@endsection
