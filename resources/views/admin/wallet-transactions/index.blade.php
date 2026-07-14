@extends('layouts.admin')

@section('title', 'Wallet Transactions')
@section('page-title', 'Wallet Transactions')

@section('content')

<form method="GET" class="mb-3 d-flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search reference...">
    <select name="type" class="form-select form-select-sm">
        <option value="">All Types</option>
        <option value="recharge" @selected(request('type')==='recharge')>Recharge</option>
        <option value="debit" @selected(request('type')==='debit')>Debit</option>
        <option value="refund" @selected(request('type')==='refund')>Refund</option>
    </select>
    <button class="btn btn-sm btn-outline-secondary">Filter</button>
</form>

<div class="admin-panel-box p-3">
    <table class="table align-middle">
        <thead>
            <tr><th>Reference</th><th>User</th><th>Type</th><th>Amount</th><th>Balance After</th><th>Date</th></tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td class="small">{{ $tx->reference }}</td>
                    <td class="small">{{ $tx->wallet->user->name ?? 'N/A' }}</td>
                    <td><span class="badge bg-{{ $tx->type === 'recharge' ? 'success' : ($tx->type === 'refund' ? 'info' : 'danger') }}">{{ ucfirst($tx->type) }}</span></td>
                    <td>৳{{ number_format($tx->amount, 2) }}</td>
                    <td>৳{{ number_format($tx->balance_after, 2) }}</td>
                    <td class="small text-secondary">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">No transactions recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $transactions->links() }}
</div>
@endsection
