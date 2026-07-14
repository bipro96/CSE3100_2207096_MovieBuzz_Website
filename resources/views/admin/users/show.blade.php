@extends('layouts.admin')

@section('title', $user->name)
@section('page-title', 'User: ' . $user->name)

@section('content')

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="admin-panel-box p-3">
            <p class="mb-1"><strong>{{ $user->name }}</strong></p>
            <p class="small text-secondary mb-1">{{ $user->email }}</p>
            <p class="small text-secondary mb-1">{{ $user->phone ?? '-' }}</p>
            <p class="small mb-0">Wallet Balance: <strong>৳{{ number_format($user->wallet->balance ?? 0, 2) }}</strong></p>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="admin-panel-box p-3">
            <h6 class="fw-bold mb-3">Wallet Transactions</h6>
            @forelse($user->wallet->transactions as $tx)
                <div class="border-bottom pb-2 mb-2 small d-flex justify-content-between">
                    <span>{{ ucfirst($tx->type) }} — {{ $tx->reference }}</span>
                    <span>৳{{ number_format($tx->amount, 2) }}</span>
                </div>
            @empty
                <p class="small text-secondary">No transactions yet.</p>
            @endforelse
        </div>
    </div>
    <div class="col-md-6">
        <div class="admin-panel-box p-3">
            <h6 class="fw-bold mb-3">Bookings</h6>
            @forelse($user->bookings as $booking)
                <div class="border-bottom pb-2 mb-2 small d-flex justify-content-between">
                    <span>{{ $booking->show->movie->title ?? 'N/A' }} ({{ $booking->booking_number }})</span>
                    <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'secondary' }}">{{ ucfirst($booking->status) }}</span>
                </div>
            @empty
                <p class="small text-secondary">No bookings yet.</p>
            @endforelse
        </div>
    </div>
</div>

<a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm mt-3">Back to Users</a>
@endsection
