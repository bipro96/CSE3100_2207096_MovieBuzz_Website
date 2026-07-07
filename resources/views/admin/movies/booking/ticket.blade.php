@extends('layouts.app')

@section('title', 'Ticket - ' . $booking->booking_number)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="mb-filter-box p-4">
                <div class="text-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-clapperboard"></i> MovieBuzz Ticket</h5>
                    <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'cancelled' ? 'danger' : 'secondary') }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>

                <div class="text-center mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrPayload) }}"
                         alt="QR Code" class="border rounded p-2 bg-white">
                    <p class="small text-secondary mt-2 mb-0">Code: <strong>{{ $booking->booking_code }}</strong></p>
                </div>

                <hr>

                <p class="mb-1"><strong>{{ $booking->show->movie->title }}</strong></p>
                <p class="small text-secondary mb-1"><i class="fa-solid fa-building"></i> {{ $booking->show->cinema->name }} - {{ $booking->show->hall->name }}</p>
                <p class="small text-secondary mb-1"><i class="fa-regular fa-calendar"></i> {{ $booking->show->starts_at->format('l, d M Y') }}</p>
                <p class="small text-secondary mb-1"><i class="fa-regular fa-clock"></i> {{ $booking->show->starts_at->format('h:i A') }} &middot; {{ $booking->show->format }}</p>
                <p class="small text-secondary mb-1"><i class="fa-solid fa-chair"></i> Seats: {{ $booking->seats->pluck('seat_code')->implode(', ') }}</p>
                <p class="small text-secondary mb-0"><i class="fa-solid fa-sack-dollar"></i> Amount: ৳{{ number_format($booking->total_amount, 2) }}</p>

                <div class="text-center mt-4">
                    <a href="{{ route('booking.history') }}" class="btn btn-outline-secondary btn-sm">Back to My Bookings</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
