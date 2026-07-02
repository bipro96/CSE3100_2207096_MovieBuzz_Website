@extends('layouts.admin')

@section('title', 'Genres')
@section('page-title', 'Manage Genres')

@section('content')

<div class="row g-4">
    <div class="col-md-4">
        <div class="admin-panel-box p-3">
            <h6 class="fw-bold mb-3">Add Genre</h6>
            <form method="POST" action="{{ route('admin.genres.store') }}" class="d-flex gap-2">
                @csrf
                <input type="text" name="name" class="form-control" placeholder="Genre name" required>
                <button class="btn btn-warning">Add</button>
            </form>
        </div>
    </div>

    <div class="col-md-8">
        <div class="admin-panel-box p-3">
            <table class="table align-middle">
                <thead>
                    <tr><th>Name</th><th>Movies</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($genres as $genre)
                        <tr>
                            <td>
                                <form method="POST" action="{{ route('admin.genres.update', $genre) }}" class="d-flex gap-2">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $genre->name }}" class="form-control form-control-sm">
                                    <button class="btn btn-sm btn-outline-primary">Save</button>
                                </form>
                            </td>
                            <td>{{ $genre->movies_count }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.genres.destroy', $genre) }}" onsubmit="return confirm('Delete this genre?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                        @if($genre->movies_count > 0) disabled title="Cannot delete: still assigned to movies" @endif>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-secondary py-4">No genres yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $genres->links() }}
        </div>
    </div>
</div>
@endsection
