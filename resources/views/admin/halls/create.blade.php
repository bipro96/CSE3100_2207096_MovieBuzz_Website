@extends('layouts.admin')

@section('title', 'Add Hall')
@section('page-title', 'Add Hall')

@section('content')
<div class="admin-panel-box p-4" style="max-width:600px">
    <form method="POST" action="{{ route('admin.halls.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Cinema <span class="text-danger">*</span></label>
            <select name="cinema_id" class="form-select" required>
                <option value="">Select cinema</option>
                @foreach($cinemas as $cinema)
                    <option value="{{ $cinema->id }}" @selected(old('cinema_id') == $cinema->id)>{{ $cinema->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Hall Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="e.g. Hall 1" value="{{ old('name') }}" required>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">Rows <span class="text-danger">*</span></label>
                <input type="number" name="rows" class="form-control" min="1" max="26" value="{{ old('rows', 8) }}" required>
            </div>
            <div class="col-6">
                <label class="form-label">Columns <span class="text-danger">*</span></label>
                <input type="number" name="columns" class="form-control" min="1" max="40" value="{{ old('columns', 12) }}" required>
            </div>
        </div>
        <p class="small text-secondary">A default "regular" seat grid will be generated. You can customize seat types (Premium / VIP / Disabled / Unavailable) on the next screen.</p>
        <button class="btn btn-warning px-4">Create Hall &amp; Build Layout</button>
        <a href="{{ route('admin.halls.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </form>
</div>
@endsection
