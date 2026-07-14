@extends('layouts.admin')

@section('title', 'Edit Hall')
@section('page-title', 'Edit Hall')

@section('content')
<div class="admin-panel-box p-4" style="max-width:600px">
    <form method="POST" action="{{ route('admin.halls.update', $hall) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Cinema <span class="text-danger">*</span></label>
            <select name="cinema_id" class="form-select" required>
                @foreach($cinemas as $cinema)
                    <option value="{{ $cinema->id }}" @selected($hall->cinema_id == $cinema->id)>{{ $cinema->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Hall Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $hall->name) }}" required>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked($hall->is_active)>
            <label class="form-check-label" for="isActive">Active</label>
        </div>
        <p class="small text-secondary">To change the seat layout, use the <a href="{{ route('admin.halls.layout', $hall) }}">Layout editor</a> instead.</p>
        <button class="btn btn-warning px-4">Update Hall</button>
        <a href="{{ route('admin.halls.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </form>
</div>
@endsection
