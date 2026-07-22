<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - MovieBuzz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="admin-body">
<div class="admin-wrapper d-flex">
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-clapperboard"></i> Movie<span class="text-warning">Buzz</span></a>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
            <a href="{{ route('admin.movies.index') }}" class="{{ request()->routeIs('admin.movies.*') ? 'active' : '' }}">
                <i class="fa-solid fa-film"></i> Movies
            </a>
            <a href="{{ route('admin.genres.index') }}" class="{{ request()->routeIs('admin.genres.*') ? 'active' : '' }}"><i class="fa-solid fa-tags"></i> Genres</a>
            <a href="{{ route('admin.cinemas.index') }}" class="{{ request()->routeIs('admin.cinemas.*') ? 'active' : '' }}"><i class="fa-solid fa-building"></i> Cinemas</a>
            <a href="{{ route('admin.halls.index') }}" class="{{ request()->routeIs('admin.halls.*') ? 'active' : '' }}"><i class="fa-solid fa-door-open"></i> Halls</a>
            <a href="{{ route('admin.shows.index') }}" class="{{ request()->routeIs('admin.shows.*') ? 'active' : '' }}"><i class="fa-solid fa-calendar-days"></i> Shows</a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Users</a>
            <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"><i class="fa-solid fa-ticket"></i> Bookings</a>
            <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"><i class="fa-solid fa-star"></i> Reviews</a>
            <a href="{{ route('admin.wallet-transactions.index') }}" class="{{ request()->routeIs('admin.wallet-transactions.*') ? 'active' : '' }}"><i class="fa-solid fa-wallet"></i> Wallet Transactions</a>
            <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> Settings</a>
        </nav>
    </aside>

    <div class="admin-content flex-grow-1">
        <header class="admin-topbar d-flex align-items-center justify-content-between">
            <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="text-decoration-none small"><i class="fa-solid fa-arrow-up-right-from-square"></i> View Site</a>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle text-decoration-none" data-bs-toggle="dropdown">
                        {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="admin-body-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
