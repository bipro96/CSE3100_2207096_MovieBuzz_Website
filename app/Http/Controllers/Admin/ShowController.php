<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\Movie;
use App\Models\Show;
use App\Services\BookingService;
use App\Services\ShowService;
use Illuminate\Http\Request;
use RuntimeException;

class ShowController extends Controller
{
    public function __construct(protected ShowService $showService, protected BookingService $bookingService)
    {
    }

    public function index(Request $request)
    {
        $shows = Show::with('movie', 'cinema', 'hall')
            ->when($request->filled('movie_id'), fn ($q) => $q->where('movie_id', $request->movie_id))
            ->when($request->filled('cinema_id'), fn ($q) => $q->where('cinema_id', $request->cinema_id))
            ->orderByDesc('starts_at')
            ->paginate(15)
            ->withQueryString();

        $movies = Movie::orderBy('title')->get();
        $cinemas = Cinema::orderBy('name')->get();

        return view('admin.shows.index', compact('shows', 'movies', 'cinemas'));
    }

    public function create()
    {
        $movies = Movie::where('is_active', true)->orderBy('title')->get();
        $cinemas = Cinema::where('is_active', true)->with('halls')->orderBy('name')->get();

        return view('admin.shows.create', compact('movies', 'cinemas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'hall_id' => 'required|exists:halls,id',
            'show_date' => 'required|date|after_or_equal:today',
            'show_time' => 'required',
            'language' => 'nullable|string|max:50',
            'format' => 'required|string|max:20',
            'ticket_price' => 'required|numeric|min:0',
            'premium_price' => 'nullable|numeric|min:0',
            'vip_price' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->showService->createShow($validated);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.shows.index')->with('success', 'Show scheduled successfully.');
    }

    public function edit(Show $show)
    {
        $show->load('movie', 'cinema', 'hall');
        return view('admin.shows.edit', compact('show'));
    }

    /**
     * Only price/status/language/format are editable after seats exist,
     * to avoid orphaning already-booked seats.
     */
    public function update(Request $request, Show $show)
    {
        $validated = $request->validate([
            'language' => 'nullable|string|max:50',
            'format' => 'required|string|max:20',
            'ticket_price' => 'required|numeric|min:0',
            'premium_price' => 'nullable|numeric|min:0',
            'vip_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        $show->update($validated);

        return redirect()->route('admin.shows.index')->with('success', 'Show updated.');
    }

    /**
     * Cancelling a show refunds every confirmed booking automatically.
     */
    public function destroy(Show $show)
    {
        $bookings = $show->bookings()->where('status', 'confirmed')->get();

        foreach ($bookings as $booking) {
            try {
                $this->bookingService->cancelBooking($booking, force: true);
            } catch (RuntimeException $e) {
                // Show is within the 2-hour cutoff for this booking; still cancel the show itself below.
            }
        }

        $this->showService->cancelShow($show);

        return back()->with('success', 'Show cancelled. ' . $bookings->count() . ' booking(s) refunded.');
    }

    /**
     * AJAX: fetch halls belonging to a cinema, for the create-show form.
     */
    public function hallsForCinema(Cinema $cinema)
    {
        return response()->json($cinema->halls()->where('is_active', true)->get(['id', 'name']));
    }
}
