@extends('layouts.admin')

@section('title', 'Cinemas')
@section('page-title', 'Manage Cinemas')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.cinemas.create') }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-plus"></i> Add Cinema</a>
</div>

<div class="admin-panel-box p-3">
    <table class="table align-middle">
        <thead>
            <tr><th>Name</th><th>Address</th><th>Location</th><th>Halls</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($cinemas as $cinema)
                <tr>
                    <td>{{ $cinema->name }}</td>
                    <td class="small">{{ $cinema->address }}</td>
                    <td class="small">{{ $cinema->location }}</td>
                    <td>{{ $cinema->halls_count }}</td>
                    <td><span class="badge bg-{{ $cinema->is_active ? 'success' : 'secondary' }}">{{ $cinema->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <a href="{{ route('admin.cinemas.edit', $cinema) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.cinemas.destroy', $cinema) }}" class="d-inline" onsubmit="return confirm('Delete this cinema? This also removes its halls and shows.')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">No cinemas added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $cinemas->links() }}
</div>
@endsection
