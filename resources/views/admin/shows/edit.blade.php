@extends('layouts.admin')

@section('title', 'Edit Show')
@section('page-title', 'Edit Show')

@section('content')
<div class="admin-panel-box p-4" style="max-width:600px">
    <p class="small text-secondary">
        <strong>{{ $show->movie->title }}</strong> &middot; {{ $show->cinema->name }} - {{ $show->hall->name }} &middot;
        {{ $show->starts_at->format('d M Y, h:i A') }}
    </p>
    <p class="small text-warning">Date, hall, and movie can't be changed after seats are generated — cancel and reschedule instead if needed.</p>

    <form method="POST" action="{{ route('admin.shows.update', $show) }}">
        @csrf @method('PUT')

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Language</label>
                <input type="text" name="language" class="form-control" value="{{ old('language', $show->language) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Format</label>
                <select name="format" class="form-select">
                    @foreach(['2D','3D','IMAX','4DX'] as $fmt)
                        <option value="{{ $fmt }}" @selected($show->format===$fmt)>{{ $fmt }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Regular Price</label>
                <input type="number" step="0.01" name="ticket_price" class="form-control" value="{{ old('ticket_price', $show->ticket_price) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Premium Price</label>
                <input type="number" step="0.01" name="premium_price" class="form-control" value="{{ old('premium_price', $show->premium_price) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">VIP Price</label>
                <input type="number" step="0.01" name="vip_price" class="form-control" value="{{ old('vip_price', $show->vip_price) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                @foreach(['scheduled','ongoing','completed','cancelled'] as $status)
                    <option value="{{ $status }}" @selected($show->status===$status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-warning px-4">Update Show</button>
        <a href="{{ route('admin.shows.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </form>
</div>
@endsection
