<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CinemaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\HallController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ShowController as AdminShowController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WalletTransactionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;


//Public / Customer routes


Route::get('/', [MovieController::class, 'home'])->name('home');
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/search', [MovieController::class, 'search'])->name('movies.search'); // AJAX
Route::get('/movies/{slug}', [MovieController::class, 'show'])->name('movies.show');



Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

//customer routes


Route::middleware('auth')->group(function () {
    Route::post('/movies/{movie}/wishlist', [MovieController::class, 'toggleWishlist'])->name('movies.wishlist.toggle');
    Route::get('/wishlist', [MovieController::class, 'wishlist'])->name('wishlist.index');

    Route::post('/movies/{movie}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/recharge', [WalletController::class, 'recharge'])->name('wallet.recharge');

    // Booking flow
    Route::get('/shows/{show}/seats', [BookingController::class, 'seats'])->name('booking.seats');
    Route::post('/shows/{show}/lock', [BookingController::class, 'lock'])->name('booking.lock');
    Route::get('/shows/{show}/summary', [BookingController::class, 'summary'])->name('booking.summary');
    Route::post('/shows/{show}/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::post('/shows/{show}/cancel-hold', [BookingController::class, 'cancelHold'])->name('booking.cancel-hold');
    Route::get('/bookings/history', [BookingController::class, 'history'])->name('booking.history');
    Route::get('/bookings/{booking}/success', [BookingController::class, 'success'])->name('booking.success');
    Route::get('/bookings/{booking}/ticket', [BookingController::class, 'ticket'])->name('booking.ticket');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
});


// Admin routes


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/movies/tmdb-search', [AdminMovieController::class, 'tmdbSearch'])->name('movies.tmdb-search');
    Route::get('/movies/tmdb-fetch', [AdminMovieController::class, 'tmdbFetch'])->name('movies.tmdb-fetch');
    Route::post('/movies/{movie}/toggle-active', [AdminMovieController::class, 'toggleActive'])->name('movies.toggle-active');
    Route::post('/movies/{movie}/toggle-featured', [AdminMovieController::class, 'toggleFeatured'])->name('movies.toggle-featured');
    Route::resource('movies', AdminMovieController::class)->except(['show']);

    Route::resource('cinemas', CinemaController::class)->except(['show']);

    Route::get('/halls/{hall}/layout', [HallController::class, 'layout'])->name('halls.layout');
    Route::post('/halls/{hall}/layout', [HallController::class, 'updateLayout'])->name('halls.layout.update');
    Route::resource('halls', HallController::class)->except(['show']);

    Route::get('/shows/halls-for-cinema/{cinema}', [AdminShowController::class, 'hallsForCinema'])->name('shows.halls-for-cinema');
    Route::resource('shows', AdminShowController::class)->except(['show']);

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');

    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/wallet-transactions', [WalletTransactionController::class, 'index'])->name('wallet-transactions.index');

    Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
    Route::post('/genres', [GenreController::class, 'store'])->name('genres.store');
    Route::put('/genres/{genre}', [GenreController::class, 'update'])->name('genres.update');
    Route::delete('/genres/{genre}', [GenreController::class, 'destroy'])->name('genres.destroy');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
});
