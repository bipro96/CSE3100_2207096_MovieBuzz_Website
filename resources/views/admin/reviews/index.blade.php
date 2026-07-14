@extends('layouts.admin')

@section('title', 'Reviews')
@section('page-title', 'Moderate Reviews')

@section('content')

<form method="GET" class="mb-3 d-flex gap-2">
    <select name="status" class="form-select form-select-sm">
        <option value="">All Status</option>
        <option value="pending" @selected(request('status')==='pending')>Pending</option>
        <option value="approved" @selected(request('status')==='approved')>Approved</option>
        <option value="rejected" @selected(request('status')==='rejected')>Rejected</option>
    </select>
    <button class="btn btn-sm btn-outline-secondary">Filter</button>
</form>

<div class="admin-panel-box p-3">
    <table class="table align-middle">
        <thead>
            <tr><th>User</th><th>Movie</th><th>Rating</th><th>Comment</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
                <tr>
                    <td class="small">{{ $review->user->name }}</td>
                    <td class="small">{{ $review->movie->title }}</td>
                    <td>{{ $review->rating }} <i class="fa-solid fa-star text-warning"></i></td>
                    <td class="small">{{ \Illuminate\Support\Str::limit($review->comment, 60) }}</td>
                    <td><span class="badge bg-{{ $review->status === 'approved' ? 'success' : ($review->status === 'rejected' ? 'danger' : 'secondary') }}">{{ ucfirst($review->status) }}</span></td>
                    <td class="d-flex gap-1">
                        @if($review->status !== 'approved')
                            <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-success"><i class="fa-solid fa-check"></i></button>
                            </form>
                        @endif
                        @if($review->status !== 'rejected')
                            <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">No reviews submitted yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $reviews->links() }}
</div>
@endsection
