@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="admin-stat-card">
            <i class="fa-solid fa-film"></i>
            <div>
                <h4>{{ $stats['total_movies'] }}</h4>
                <p>Total Movies</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-stat-card">
            <i class="fa-solid fa-users"></i>
            <div>
                <h4>{{ $stats['total_users'] }}</h4>
                <p>Total Users</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-stat-card">
            <i class="fa-solid fa-ticket"></i>
            <div>
                <h4>{{ $stats['today_bookings'] }}</h4>
                <p>Today's Bookings</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="admin-stat-card">
            <i class="fa-solid fa-sack-dollar"></i>
            <div>
                <h4>৳{{ number_format($stats['today_revenue'], 2) }}</h4>
                <p>Today's Revenue</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="admin-panel-box p-3">
            <h6 class="fw-bold mb-3">Total Wallet Balance</h6>
            <h2 class="text-warning">৳{{ number_format($stats['total_wallet_balance'], 2) }}</h2>
            <p class="small text-secondary mb-0">Sum of all customer wallet balances (demo currency).</p>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="admin-panel-box p-3">
            <h6 class="fw-bold mb-3">Upcoming Shows</h6>
            @forelse($upcomingShows as $show)
                <div class="border-bottom pb-2 mb-2 small">
                    <strong>{{ $show->movie->title }}</strong><br>
                    {{ $show->cinema->name }} - {{ $show->hall->name }}<br>
                    <span class="text-secondary">{{ $show->starts_at->format('d M, h:i A') }}</span>
                </div>
            @empty
                <p class="small text-secondary">No upcoming shows scheduled.</p>
            @endforelse
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-panel-box p-3">
            <h6 class="fw-bold mb-3">Latest Bookings</h6>
            @forelse($latestBookings as $booking)
                <div class="border-bottom pb-2 mb-2 small">
                    <strong>{{ $booking->booking_number }}</strong><br>
                    {{ $booking->user->name }} - {{ $booking->show->movie->title ?? 'N/A' }}<br>
                    <span class="text-secondary">৳{{ number_format($booking->total_amount, 2) }}</span>
                </div>
            @empty
                <p class="small text-secondary">No bookings yet.</p>
            @endforelse
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-panel-box p-3">
            <h6 class="fw-bold mb-3">Latest Registered Users</h6>
            @forelse($latestUsers as $user)
                <div class="border-bottom pb-2 mb-2 small">
                    <strong>{{ $user->name }}</strong><br>
                    <span class="text-secondary">{{ $user->email }}</span>
                </div>
            @empty
                <p class="small text-secondary">No users yet.</p>
            @endforelse
        </div>
    </div>
</div>

@endsection
