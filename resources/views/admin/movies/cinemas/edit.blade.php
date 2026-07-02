@extends('layouts.admin')

@section('title', 'Edit Cinema')
@section('page-title', 'Edit Cinema')

@section('content')
<div class="admin-panel-box p-4" style="max-width:600px">
    <form method="POST" action="{{ route('admin.cinemas.update', $cinema) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="form-label">Cinema Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $cinema->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Address <span class="text-danger">*</span></label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $cinema->address) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Location / City</label>
            <input type="text" name="location" class="form-control" value="{{ old('location', $cinema->location) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $cinema->phone) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $cinema->email) }}">
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked($cinema->is_active)>
            <label class="form-check-label" for="isActive">Active</label>
        </div>
        <button class="btn btn-warning px-4">Update Cinema</button>
        <a href="{{ route('admin.cinemas.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </form>
</div>
@endsection
