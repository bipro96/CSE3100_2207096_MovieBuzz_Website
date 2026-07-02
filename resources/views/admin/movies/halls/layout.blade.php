@extends('layouts.admin')

@section('title', 'Seat Layout - ' . $hall->name)
@section('page-title', 'Seat Layout: ' . $hall->name . ' (' . $hall->cinema->name . ')')

@section('content')

<div class="admin-panel-box p-4">
    <p class="small text-secondary">Click a seat to cycle its type: Regular &rarr; Premium &rarr; VIP &rarr; Disabled &rarr; Unavailable &rarr; Regular.</p>

    <div class="d-flex gap-3 mb-3 small">
        <span><span class="layout-swatch" style="background:#3a3d55"></span> Regular</span>
        <span><span class="layout-swatch" style="background:#c9a04a"></span> Premium</span>
        <span><span class="layout-swatch" style="background:#b06fd1"></span> VIP</span>
        <span><span class="layout-swatch" style="background:#5b8def"></span> Disabled</span>
        <span><span class="layout-swatch" style="background:#2a2c3a;border:1px dashed #555"></span> Unavailable</span>
    </div>

    <div class="text-center mb-4">
        <div class="mb-screen">SCREEN</div>
    </div>

    <div id="layoutGrid" class="d-flex flex-column align-items-center gap-2">
        @foreach($seatsByRow as $rowLabel => $rowSeats)
            <div class="d-flex align-items-center gap-2">
                <span class="seat-row-label">{{ $rowLabel }}</span>
                @foreach($rowSeats->sortBy('column_number') as $seat)
                    <button type="button"
                        class="layout-seat layout-seat-{{ $seat->seat_type }}"
                        data-id="{{ $seat->id }}"
                        data-type="{{ $seat->seat_type }}">
                        {{ $seat->column_number }}
                    </button>
                @endforeach
            </div>
        @endforeach
    </div>

    <div class="mt-4 d-flex align-items-center gap-3">
        <button id="saveLayoutBtn" class="btn btn-warning px-4">Save Layout</button>
        <a href="{{ route('admin.halls.index') }}" class="btn btn-outline-secondary px-4">Back to Halls</a>
        <span id="layoutStatus" class="small text-success"></span>
    </div>
</div>

@endsection

@push('styles')
<style>
.mb-screen { background:#33355a;color:#fff;padding:8px;border-radius:100px 100px 0 0;width:60%;margin:0 auto;letter-spacing:4px;font-size:.8rem; }
.seat-row-label { width: 24px; text-align: center; color: #999; font-size: .8rem; }
.layout-swatch { display:inline-block; width:14px; height:14px; border-radius:3px; margin-right:4px; vertical-align:middle; }
.layout-seat {
    width: 30px; height: 30px; border-radius: 5px; border: none; font-size: .65rem; color: #fff; cursor: pointer;
}
.layout-seat-regular { background: #3a3d55; }
.layout-seat-premium { background: #c9a04a; }
.layout-seat-vip { background: #b06fd1; }
.layout-seat-disabled { background: #5b8def; }
.layout-seat-unavailable { background: #2a2c3a; border: 1px dashed #555 !important; color: #555; }
</style>
@endpush

@push('scripts')
<script>
const cycle = ['regular', 'premium', 'vip', 'disabled', 'unavailable'];

document.querySelectorAll('.layout-seat').forEach(btn => {
    btn.addEventListener('click', () => {
        const current = btn.dataset.type;
        const next = cycle[(cycle.indexOf(current) + 1) % cycle.length];
        btn.dataset.type = next;
        btn.className = 'layout-seat layout-seat-' + next;
    });
});

document.getElementById('saveLayoutBtn').addEventListener('click', () => {
    const seats = Array.from(document.querySelectorAll('.layout-seat')).map(btn => ({
        id: btn.dataset.id,
        seat_type: btn.dataset.type,
    }));

    const statusEl = document.getElementById('layoutStatus');
    statusEl.innerText = 'Saving...';

    fetch("{{ route('admin.halls.layout.update', $hall) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ seats }),
    })
    .then(r => r.json())
    .then(data => { statusEl.innerText = data.message || 'Saved.'; })
    .catch(() => { statusEl.innerText = 'Failed to save layout.'; statusEl.classList.replace('text-success','text-danger'); });
});
</script>
@endpush
