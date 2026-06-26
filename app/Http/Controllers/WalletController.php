<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    protected const RECHARGE_OPTIONS = [200, 500, 1000, 2000];

    public function __construct(protected WalletService $walletService)
    {
    }

    public function index()
    {
        $wallet = $this->walletService->walletFor(auth()->user());
        $transactions = $wallet->transactions()->paginate(15);

        return view('wallet.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'rechargeOptions' => self::RECHARGE_OPTIONS,
        ]);
    }

    public function recharge(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', Rule::in(self::RECHARGE_OPTIONS)],
        ]);

        $this->walletService->recharge(auth()->user(), (float) $validated['amount']);

        if ($request->wantsJson()) {
            $wallet = $this->walletService->walletFor(auth()->user());
            return response()->json(['balance' => $wallet->fresh()->balance]);
        }

        return back()->with('success', 'Wallet recharged with ৳' . number_format($validated['amount'], 2));
    }
}
