@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Manage Users')

@section('content')

<form method="GET" class="mb-3 d-flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search name or email...">
    <button class="btn btn-sm btn-outline-secondary">Search</button>
</form>

<div class="admin-panel-box p-3">
    <table class="table align-middle">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Wallet Balance</th><th>Bookings</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td class="small">{{ $user->email }}</td>
                    <td class="small">{{ $user->phone ?? '-' }}</td>
                    <td>৳{{ number_format($user->wallet->balance ?? 0, 2) }}</td>
                    <td>{{ $user->bookings_count }}</td>
                    <td><span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Active' : 'Deactivated' }}</span></td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a>
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-outline-{{ $user->is_active ? 'danger' : 'success' }}">
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">No customers registered yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
