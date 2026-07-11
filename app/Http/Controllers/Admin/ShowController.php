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

    public function update(Request $request, Show $show)
    {
        $validated = $request->validate([
            'language' => 'nullable|string|max:50',
            'format' => 'required|string|max:20',
       
        ]);

        $show->update($validated);

        return redirect()->route('admin.shows.index')->with('success', 'Show updated.');
    }

    public function destroy(Show $show)
    {
        $bookings = $show->bookings()->where('status', 'confirmed')->get();

        foreach ($bookings as $booking) {
            try {
                $this->bookingService->cancelBooking($booking, force: true);
            } catch (RuntimeException $e) {
              
            }
        }

        $this->showService->cancelShow($show);

        return back()->with('success', 'Show cancelled. ' . $bookings->count() . ' booking(s) refunded.');
    }

  
   
}
