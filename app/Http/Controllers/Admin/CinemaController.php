<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Cinema;
use App\Models\Show;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    public function index()
    {
        $cinemas = Cinema::withCount('halls')->latest()->paginate(15);
        return view('admin.cinemas.index', compact('cinemas'));
    }

    public function create()
    {
        return view('admin.cinemas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        Cinema::create($validated);

        return redirect()->route('admin.cinemas.index')->with('success', 'Cinema added.');
    }

    public function edit(Cinema $cinema)
    {
        return view('admin.cinemas.edit', compact('cinema'));
    }

    public function update(Request $request, Cinema $cinema)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $cinema->update($validated);

        return redirect()->route('admin.cinemas.index')->with('success', 'Cinema updated.');
    }

    public function destroy(Cinema $cinema)
    {
        $showIds = Show::where('cinema_id', $cinema->id)->pluck('id');
        $hasBookings = Booking::whereIn('show_id', $showIds)->exists();

        if ($hasBookings) {
            return back()->with('error', 'Cannot delete "' . $cinema->name . '" - it has shows with existing bookings. Set it to Inactive instead so booking history stays intact.');
        }

        $cinema->delete(); // halls/hall_seats/shows/show_seats cascade-delete automatically
        return back()->with('success', 'Cinema deleted.');
    }
}
