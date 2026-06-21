@extends('layouts.app')

@section('title', 'Login - MovieBuzz')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="mb-filter-box p-4">
                <h3 class="fw-bold mb-4 text-center">Welcome Back</h3>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button class="btn btn-warning w-100 mb-3">Login</button>
                    <div class="d-flex justify-content-between small">
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                        <a href="{{ route('register') }}">Create an account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
