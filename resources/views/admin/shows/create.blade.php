@extends('layouts.admin')

@section('title', 'Schedule Show')
@section('page-title', 'Schedule Show')

@section('content')
<div class="admin-panel-box p-4" style="max-width:700px">
    <form method="POST" action="{{ route('admin.shows.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Movie <span class="text-danger">*</span></label>
            <select name="movie_id" class="form-select" required>
                <option value="">Select movie</option>
                @foreach($movies as $movie)
                    <option value="{{ $movie->id }}" @selected(old('movie_id')==$movie->id)>{{ $movie->title }} ({{ $movie->runtime ?? '?' }} min)</option>
                @endforeach
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Cinema <span class="text-danger">*</span></label>
                <select id="cinemaSelect" class="form-select" required>
                    <option value="">Select cinema</option>
                    @foreach($cinemas as $cinema)
                        <option value="{{ $cinema->id }}">{{ $cinema->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Hall <span class="text-danger">*</span></label>
                <select name="hall_id" id="hallSelect" class="form-select" required disabled>
                    <option value="">Select cinema first</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Show Date <span class="text-danger">*</span></label>
                <input type="date" name="show_date" class="form-control" min="{{ date('Y-m-d') }}" value="{{ old('show_date') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Show Time <span class="text-danger">*</span></label>
                <input type="time" name="show_time" class="form-control" value="{{ old('show_time') }}" required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Language</label>
                <input type="text" name="language" class="form-control" value="{{ old('language', 'English') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Format <span class="text-danger">*</span></label>
                <select name="format" class="form-select" required>
                    <option value="2D">2D</option>
                    <option value="3D">3D</option>
                    <option value="IMAX">IMAX</option>
                    <option value="4DX">4DX</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Regular Price <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="ticket_price" class="form-control" value="{{ old('ticket_price') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Premium Price</label>
                <input type="number" step="0.01" name="premium_price" class="form-control" value="{{ old('premium_price') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">VIP Price</label>
                <input type="number" step="0.01" name="vip_price" class="form-control" value="{{ old('vip_price') }}">
            </div>
        </div>

        <p class="small text-secondary">Overlapping shows in the same hall are automatically blocked (movie runtime + 20 min buffer is reserved).</p>

        <button class="btn btn-warning px-4">Schedule Show</button>
        <a href="{{ route('admin.shows.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
const cinemaSelect = document.getElementById('cinemaSelect');
const hallSelect = document.getElementById('hallSelect');

cinemaSelect.addEventListener('change', () => {
    const cinemaId = cinemaSelect.value;
    hallSelect.innerHTML = '<option value="">Loading...</option>';
    hallSelect.disabled = true;

    if (!cinemaId) {
        hallSelect.innerHTML = '<option value="">Select cinema first</option>';
        return;
    }

    fetch(`/admin/shows/halls-for-cinema/${cinemaId}`)
        .then(r => r.json())
        .then(halls => {
            if (halls.length === 0) {
                hallSelect.innerHTML = '<option value="">No halls found for this cinema</option>';
                return;
            }
            hallSelect.innerHTML = halls.map(h => `<option value="${h.id}">${h.name}</option>`).join('');
            hallSelect.disabled = false;
        });
});
</script>
@endpush
