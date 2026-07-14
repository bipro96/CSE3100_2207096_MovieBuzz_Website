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

        <div class="col-lg-8">
            <h5 class="fw-bold mb-3">Transaction History</h5>
            <div class="table-responsive mb-filter-box p-3">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Balance After</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td class="small">{{ $tx->reference }}</td>
                                <td>
                                    <span class="badge bg-{{ $tx->type === 'recharge' ? 'success' : ($tx->type === 'refund' ? 'info' : 'danger') }}">
                                        {{ ucfirst($tx->type) }}
                                    </span>
                                </td>
                                <td>৳{{ number_format($tx->amount, 2) }}</td>
                                <td>৳{{ number_format($tx->balance_after, 2) }}</td>
                                <td class="small text-secondary">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">No transactions yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
