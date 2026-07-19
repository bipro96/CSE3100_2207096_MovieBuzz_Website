<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Cinema;
use App\Models\Hall;
use App\Models\HallSeat;
use App\Models\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HallController extends Controller
{
    public function index()
    {
        $halls = Hall::with('cinema')->withCount('seats')->latest()->paginate(15);
        return view('admin.halls.index', compact('halls'));
    }

    public function create()
    {
        $cinemas = Cinema::orderBy('name')->get();
        return view('admin.halls.create', compact('cinemas'));
    }

    /**
     * Create the hall and generate a default seat grid (all "regular"),
     * which the admin then customizes on the layout editor page.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cinema_id' => 'required|exists:cinemas,id',
            'name' => 'required|string|max:100',
            'rows' => 'required|integer|min:1|max:26',
            'columns' => 'required|integer|min:1|max:40',
        ]);

        $hall = DB::transaction(function () use ($validated) {
            $hall = Hall::create([
                'cinema_id' => $validated['cinema_id'],
                'name' => $validated['name'],
                'rows' => $validated['rows'],
                'columns' => $validated['columns'],
                'capacity' => $validated['rows'] * $validated['columns'],
            ]);

            for ($r = 0; $r < $validated['rows']; $r++) {
                $rowLabel = $this->rowLabel($r);
                for ($c = 1; $c <= $validated['columns']; $c++) {
                    HallSeat::create([
                        'hall_id' => $hall->id,
                        'row_label' => $rowLabel,
                        'column_number' => $c,
                        'seat_code' => $rowLabel . $c,
                        'seat_type' => 'regular',
                    ]);
                }
            }

            return $hall;
        });

        return redirect()->route('admin.halls.layout', $hall)->with('success', 'Hall created. Now customize the seat layout.');
    }

    protected function rowLabel(int $index): string
    {
        // 0 -> A, 1 -> B ... 25 -> Z, 26 -> AA ...
        $label = '';
        do {
            $label = chr(65 + ($index % 26)) . $label;
            $index = intdiv($index, 26) - 1;
        } while ($index >= 0);
        return $label;
    }

    /**
     * Visual seat layout editor — admin clicks seats to cycle their type.
     */
    public function layout(Hall $hall)
    {
        $hall->load('cinema', 'seats');
        $seatsByRow = $hall->seats->groupBy('row_label');
        return view('admin.halls.layout', compact('hall', 'seatsByRow'));
    }

    /**
     * AJAX: save the full seat type map from the layout editor.
     */
    public function updateLayout(Request $request, Hall $hall)
    {
        $validated = $request->validate([
            'seats' => 'required|array',
            'seats.*.id' => 'required|exists:hall_seats,id',
            'seats.*.seat_type' => 'required|in:regular,premium,vip,disabled,unavailable',
        ]);

        DB::transaction(function () use ($validated, $hall) {
            foreach ($validated['seats'] as $seatData) {
                HallSeat::where('id', $seatData['id'])->where('hall_id', $hall->id)
                    ->update(['seat_type' => $seatData['seat_type']]);
            }
        });

        return response()->json(['message' => 'Layout saved successfully.']);
    }

    public function edit(Hall $hall)
    {
        $cinemas = Cinema::orderBy('name')->get();
        return view('admin.halls.edit', compact('hall', 'cinemas'));
    }

    public function update(Request $request, Hall $hall)
    {
        $validated = $request->validate([
            'cinema_id' => 'required|exists:cinemas,id',
            'name' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $hall->update($validated);

        return redirect()->route('admin.halls.index')->with('success', 'Hall updated.');
    }

    public function destroy(Hall $hall)
    {
        $showIds = Show::where('hall_id', $hall->id)->pluck('id');
        $hasBookings = Booking::whereIn('show_id', $showIds)->exists();

        if ($hasBookings) {
            return back()->with('error', 'Cannot delete "' . $hall->name . '" - it has shows with existing bookings. Set it to Inactive instead so booking history stays intact.');
        }

        $hall->delete(); // hall_seats/shows/show_seats cascade-delete automatically
        return back()->with('success', 'Hall deleted.');
    }
}
