<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MovieBuzz - Book Movie Tickets Online')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="mb-body">

<nav class="navbar navbar-expand-lg mb-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <i class="fa-solid fa-clapperboard"></i> Movie<span class="text-warning">Buzz</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto ms-lg-4">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('movies.index', ['status' => 'now_showing']) }}">Now Showing</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('movies.index', ['status' => 'upcoming']) }}">Upcoming</a></li>
            </ul>

            <form class="d-flex position-relative mb-search-form me-lg-3" role="search" action="{{ route('movies.index') }}" method="GET">
                <input id="navSearchInput" name="q" class="form-control" type="search" placeholder="Search movies..." autocomplete="off">
                <div id="navSearchResults" class="mb-search-dropdown d-none"></div>
            </form>

            <ul class="navbar-nav align-items-lg-center">
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('wallet.index') }}">
                            <i class="fa-solid fa-wallet"></i> ৳{{ number_format(auth()->user()->wallet?->balance ?? 0, 2) }}
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('wishlist.index') }}"><i class="fa-solid fa-heart"></i></a></li>
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge"></i> Admin</a></li>
                    @endif
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('booking.history') }}">My Bookings</a></li>
                            <li><a class="dropdown-item" href="{{ route('wallet.index') }}">My Wallet</a></li>
                            <li><a class="dropdown-item" href="{{ route('wishlist.index') }}">Wishlist</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="btn btn-warning btn-sm ms-lg-2 px-3" href="{{ route('register') }}">Sign Up</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main>
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @yield('content')
</main>

<footer class="mb-footer mt-5 py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5 class="fw-bold"><i class="fa-solid fa-clapperboard"></i> MovieBuzz</h5>
                <p class="text-secondary small">Your one-stop platform for booking movie tickets online — browse, pick your seats, and enjoy the show.</p>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold">Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('movies.index') }}">Browse Movies</a></li>
                    <li><a href="{{ route('wallet.index') }}">My Wallet</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold">Contact</h6>
                <p class="small text-secondary">support@moviebuzz.test</p>
            </div>
        </div>
        <hr class="border-secondary">
        <p class="text-center small text-secondary mb-0">&copy; {{ date('Y') }} MovieBuzz. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
