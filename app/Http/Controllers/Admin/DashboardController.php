<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Movie;
use App\Models\Show;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_movies' => Movie::count(),
            'total_users' => User::where('role', 'customer')->count(),
            'today_bookings' => Booking::whereDate('created_at', today())->where('status', '!=', 'cancelled')->count(),
            'today_revenue' => Booking::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total_amount'),
            'total_wallet_balance' => Wallet::sum('balance'),
        ];

        $upcomingShows = Show::with('movie', 'cinema', 'hall')
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->take(6)
            ->get();

        $latestBookings = Booking::with('user', 'show.movie')
            ->latest()
            ->take(8)
            ->get();

        $latestUsers = User::where('role', 'customer')
            ->latest()
            ->take(8)
            ->get();

        $revenueByMonth = Booking::query()
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_amount) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $popularMovies = Booking::query()
            ->join('shows', 'shows.id', '=', 'bookings.show_id')
            ->join('movies', 'movies.id', '=', 'shows.movie_id')
            ->where('bookings.status', '!=', 'cancelled')
            ->selectRaw('movies.title, COUNT(bookings.id) as bookings_count')
            ->groupBy('movies.id', 'movies.title')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();

        $popularCinemas = Booking::query()
            ->join('shows', 'shows.id', '=', 'bookings.show_id')
            ->join('cinemas', 'cinemas.id', '=', 'shows.cinema_id')
            ->where('bookings.status', '!=', 'cancelled')
            ->selectRaw('cinemas.name, COUNT(bookings.id) as bookings_count')
            ->groupBy('cinemas.id', 'cinemas.name')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'upcomingShows', 'latestBookings', 'latestUsers',
            'revenueByMonth', 'popularMovies', 'popularCinemas'
        ));
    }
}
