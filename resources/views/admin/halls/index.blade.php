@extends('layouts.admin')

@section('title', 'Halls')
@section('page-title', 'Manage Halls')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('admin.halls.create') }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-plus"></i> Add Hall</a>
</div>

<div class="admin-panel-box p-3">
    <table class="table align-middle">
        <thead>
            <tr><th>Hall</th><th>Cinema</th><th>Rows x Columns</th><th>Total Seats</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($halls as $hall)
                <tr>
                    <td>{{ $hall->name }}</td>
                    <td class="small">{{ $hall->cinema->name }}</td>
                    <td>{{ $hall->rows }} x {{ $hall->columns }}</td>
                    <td>{{ $hall->seats_count }}</td>
                    <td><span class="badge bg-{{ $hall->is_active ? 'success' : 'secondary' }}">{{ $hall->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <a href="{{ route('admin.halls.layout', $hall) }}" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-chair"></i> Layout</a>
                        <a href="{{ route('admin.halls.edit', $hall) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.halls.destroy', $hall) }}" class="d-inline" onsubmit="return confirm('Delete this hall and all its seats?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">No halls added yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $halls->links() }}
</div>
@endsection
