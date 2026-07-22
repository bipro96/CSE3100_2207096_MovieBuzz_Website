@extends('layouts.app')

@section('title', 'Booking Confirmed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="mb-filter-box p-4 text-center">
                <i class="fa-solid fa-circle-check text-success" style="font-size:3rem"></i>
                <h4 class="fw-bold mt-3">Booking Confirmed!</h4>
                <p class="text-secondary">Booking Number: <strong>{{ $booking->booking_number }}</strong></p>

                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrPayload) }}"
                     alt="Ticket QR Code" class="my-3 border rounded p-2 bg-white">

                <div class="text-start mb-filter-box p-3 mt-3">
                    <p class="mb-1"><strong>{{ $booking->show->movie->title }}</strong></p>
                    <p class="small text-secondary mb-1">{{ $booking->show->cinema->name }} &middot; {{ $booking->show->hall->name }}</p>
                    <p class="small text-secondary mb-1">{{ $booking->show->starts_at->format('l, d M Y, h:i A') }}</p>
                    <p class="small text-secondary mb-1">Seats: {{ $booking->seats->pluck('seat_code')->implode(', ') }}</p>
                    <p class="small text-secondary mb-0">Amount Paid: ৳{{ number_format($booking->total_amount, 2) }}</p>
                </div>

                <div class="d-flex gap-2 mt-4 justify-content-center">
                    <a href="{{ route('booking.ticket', $booking) }}" class="btn btn-warning">View Ticket</a>
                    <a href="{{ route('booking.history') }}" class="btn btn-outline-secondary">My Bookings</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
