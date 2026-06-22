@extends('layouts.app')

@section('title', 'Forgot Password - MovieBuzz')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="mb-filter-box p-4">
                <h3 class="fw-bold mb-3 text-center">Forgot Password</h3>
                <p class="text-secondary small text-center mb-4">Enter your email and we'll send you a password reset link.</p>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                    </div>
                    <button class="btn btn-warning w-100">Send Reset Link</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
