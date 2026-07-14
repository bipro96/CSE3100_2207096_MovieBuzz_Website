@extends('layouts.app')

@section('title', 'Booking Summary')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="mb-filter-box p-4">
                <h4 class="fw-bold mb-4">Booking Summary</h4>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="d-flex gap-3 mb-4">
                    <img src="{{ $show->movie->posterUrl() }}" style="width:80px;height:120px;object-fit:cover;border-radius:6px">
                    <div>
                        <h5 class="mb-1">{{ $show->movie->title }}</h5>
                        <p class="small text-secondary mb-0">
                            {{ $show->cinema->name }} &middot; {{ $show->hall->name }}<br>
                            {{ $show->starts_at->format('l, d M Y, h:i A') }} &middot; {{ $show->format }}
                        </p>
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr><th>Seat</th><th>Type</th><th class="text-end">Price</th></tr>
                    </thead>
                    <tbody>
                        @foreach($seats as $seat)
                            <tr>
                                <td>{{ $seat->seat_code }}</td>
                                <td class="text-capitalize">{{ $seat->seat_type }}</td>
                                <td class="text-end">৳{{ number_format($seat->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="2">Total</td>
                            <td class="text-end">৳{{ number_format($total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <div class="mb-wallet-card p-3 d-flex justify-content-between align-items-center mb-4">
                    <span>Wallet Balance</span>
                    <strong>৳{{ number_format($wallet->balance, 2) }}</strong>
                </div>

                @if($wallet->balance < $total)
                    <div class="alert alert-warning">
                        Insufficient wallet balance. Please <a href="{{ route('wallet.index') }}">recharge your wallet</a> before confirming.
                    </div>
                @endif

                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('booking.confirm', $show) }}" class="flex-grow-1">
                        @csrf
                        <input type="hidden" name="seats" value="{{ implode(',', $seatCodes) }}">
                        <button class="btn btn-warning w-100" {{ $wallet->balance < $total ? 'disabled' : '' }}>
                            <i class="fa-solid fa-wallet"></i> Pay ৳{{ number_format($total, 2) }} with Wallet
                        </button>
                    </form>
                    <form method="POST" action="{{ route('booking.cancel-hold', $show) }}">
                        @csrf
                        <input type="hidden" name="seats" value="{{ implode(',', $seatCodes) }}">
                        <button class="btn btn-outline-secondary">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
