@extends('layouts.app')

@section('title', 'Select Seats - ' . $show->movie->title)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">{{ $show->movie->title }}</h4>
            <p class="text-secondary mb-0 small">
                {{ $show->cinema->name }} &middot; {{ $show->hall->name }} &middot;
                {{ $show->starts_at->format('d M Y, h:i A') }} &middot; {{ $show->format }}
            </p>
        </div>
        <div class="text-end small">
            <span class="badge bg-secondary me-1">Available</span>
            <span class="badge" style="background:#8a8f9c">Booked</span>
            <span class="badge bg-warning text-dark">Selected</span>
            <span class="badge" style="background:#c9a04a">Premium</span>
            <span class="badge" style="background:#b06fd1">VIP</span>
        </div>
    </div>

    <div class="mb-filter-box p-4">
        <div class="text-center mb-4">
            <div class="mb-screen">SCREEN</div>
        </div>

        <div id="seatMap" class="d-flex flex-column align-items-center gap-2">
            @foreach($seatsByRow as $rowLabel => $rowSeats)
                <div class="d-flex align-items-center gap-2">
                    <span class="seat-row-label">{{ $rowLabel }}</span>
                    @foreach($rowSeats->sortBy('seat_code') as $seat)
                        @php
                            $bookable = $seat->isBookable();
                            $classes = 'mb-seat mb-seat-' . $seat->seat_type;
                            if (!$bookable) $classes .= ' mb-seat-booked';
                        @endphp
                        @if($seat->seat_type === 'unavailable')
                            <span class="mb-seat mb-seat-blank"></span>
                        @else
                            <button type="button"
                                class="{{ $classes }}"
                                data-code="{{ $seat->seat_code }}"
                                data-price="{{ $seat->price }}"
                                data-type="{{ $seat->seat_type }}"
                                {{ !$bookable ? 'disabled' : '' }}>
                                {{ $seat->seat_code }}
                            </button>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>


    

    <div class="mb-filter-box p-3 mt-3 d-flex justify-content-between align-items-center">
        <div>
            <span class="small text-secondary">Selected: </span>
            <span id="selectedSeatsText" class="fw-semibold">None</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0">Total: ৳<span id="totalPrice">0.00</span></h5>
            <button id="proceedBtn" class="btn btn-warning" disabled>Proceed to Summary</button>
        </div>
    </div>

    <div id="seatError" class="alert alert-danger mt-3 d-none"></div>
</div>
@endsection

@push('styles')
<style>
.mb-screen {
    background: #33355a; color: #fff; padding: 8px; border-radius: 100px 100px 0 0;
    width: 60%; margin: 0 auto; letter-spacing: 4px; font-size: .8rem;
}
.seat-row-label { width: 20px; text-align: center; color: #999; font-size: .8rem; }
.mb-seat {
    width: 34px; height: 34px; border-radius: 6px; border: none; font-size: .7rem;
    color: #fff; background: #3a3d55; cursor: pointer;
}
.mb-seat-blank { background: transparent; cursor: default; }
.mb-seat-premium { background: #c9a04a; }
.mb-seat-vip { background: #b06fd1; }
.mb-seat-disabled { background: #5b8def; }
.mb-seat-booked, .mb-seat:disabled { background: #23243a !important; color: #555; cursor: not-allowed; }
.mb-seat.selected { background: #f4b400 !important; color: #000; }
</style>
@endpush

@push('scripts')
<script>
const selected = new Map();
const seatMap = document.getElementById('seatMap');
const totalPriceEl = document.getElementById('totalPrice');
const selectedTextEl = document.getElementById('selectedSeatsText');
const proceedBtn = document.getElementById('proceedBtn');
const errorBox = document.getElementById('seatError');

seatMap.querySelectorAll('.mb-seat:not(.mb-seat-blank):not(:disabled)').forEach(btn => {
    btn.addEventListener('click', () => {
        const code = btn.dataset.code;
        if (selected.has(code)) {
            selected.delete(code);
            btn.classList.remove('selected');
        } else {
            if (selected.size >= 10) { alert('You can select up to 10 seats.'); return; }
            selected.set(code, parseFloat(btn.dataset.price));
            btn.classList.add('selected');
        }
        updateSummary();
    });
});



function updateSummary() {
    const codes = Array.from(selected.keys());
    selectedTextEl.innerText = codes.length ? codes.join(', ') : 'None';
    const total = Array.from(selected.values()).reduce((a, b) => a + b, 0);
    totalPriceEl.innerText = total.toFixed(2);
    proceedBtn.disabled = codes.length === 0;
}

proceedBtn.addEventListener('click', () => {
    errorBox.classList.add('d-none');
    proceedBtn.disabled = true;
    proceedBtn.innerText = 'Locking seats...';

    fetch("{{ route('booking.lock', $show) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ seats: Array.from(selected.keys()) }),
    })
    .then(async r => {
        const data = await r.json();
        if (!r.ok) throw new Error(data.message || 'Could not lock seats.');
        return data;
    })
    .then(data => { window.location.href = data.redirect; })
    .catch(err => {
        errorBox.innerText = err.message;
        errorBox.classList.remove('d-none');
        proceedBtn.disabled = false;
        proceedBtn.innerText = 'Proceed to Summary';
    });
});
</script>
@endpush


