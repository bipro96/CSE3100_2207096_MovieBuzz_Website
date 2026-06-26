@extends('layouts.app')

@section('title', 'My Wallet - MovieBuzz')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="mb-wallet-card p-4 text-white">
                <p class="opacity-75 mb-1 small">Available Balance</p>
                <h1 class="fw-bold mb-0">৳{{ number_format($wallet->balance, 2) }}</h1>
            </div>

            <div class="mt-4 p-3 mb-filter-box">
                <h6 class="fw-bold mb-3">Recharge Wallet <span class="badge bg-secondary">Demo</span></h6>
                <form method="POST" action="{{ route('wallet.recharge') }}" id="rechargeForm">
                    @csrf
                    <div class="row g-2 mb-3">
                        @foreach($rechargeOptions as $amount)
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="amount" value="{{ $amount }}" id="amt{{ $amount }}" required>
                                <label class="btn btn-outline-warning w-100" for="amt{{ $amount }}">৳{{ number_format($amount) }}</label>
                            </div>
                        @endforeach
                    </div>
                    <button class="btn btn-warning w-100">Recharge Now</button>
                </form>
            </div>
        </div>

       
</div>
@endsection
