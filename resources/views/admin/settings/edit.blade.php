@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')

<div class="row g-4">
    <div class="col-md-6">
        <div class="admin-panel-box p-4">
            <h6 class="fw-bold mb-3">Profile</h6>
            <form method="POST" action="{{ route('admin.settings.profile') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}">
                </div>
                <button class="btn btn-warning">Update Profile</button>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="admin-panel-box p-4">
            <h6 class="fw-bold mb-3">Change Password</h6>
            <form method="POST" action="{{ route('admin.settings.password') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button class="btn btn-warning">Change Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
