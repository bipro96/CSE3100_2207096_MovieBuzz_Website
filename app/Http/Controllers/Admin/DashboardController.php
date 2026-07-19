<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Movie;
use App\Models\Show;
use App\Models\User;
use App\Models\Wallet;

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

        return view('admin.dashboard', compact('stats', 'upcomingShows', 'latestBookings', 'latestUsers'));
    }
}
