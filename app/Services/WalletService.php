<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class WalletService
{
    /**
     * Get or create the wallet for a user.
     */
    public function walletFor(User $user): Wallet
    {
        return Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
    }

    /**
     * Add funds to a user's wallet (demo recharge — no gateway/approval needed).
     */
    public function recharge(User $user, float $amount): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Recharge amount must be greater than zero.');
        }

        return DB::transaction(function () use ($user, $amount) {
            $wallet = $this->walletFor($user);
            $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            $newBalance = bcadd($wallet->balance, $amount, 2);
            $wallet->update(['balance' => $newBalance]);

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'recharge',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => 'TXN-' . strtoupper(Str::random(10)),
                'description' => 'Wallet recharge',
            ]);
        });
    }

    /**
     * Debit funds from a wallet for a booking payment. Throws if insufficient balance.
     */
    public function debit(User $user, float $amount, ?Model $transactionable = null, ?string $description = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be greater than zero.');
        }

        return DB::transaction(function () use ($user, $amount, $transactionable, $description) {
            $wallet = $this->walletFor($user);
            $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            if (bccomp($wallet->balance, $amount, 2) < 0) {
                throw new RuntimeException('Insufficient wallet balance.');
            }

            $newBalance = bcsub($wallet->balance, $amount, 2);
            $wallet->update(['balance' => $newBalance]);

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => 'TXN-' . strtoupper(Str::random(10)),
                'description' => $description ?? 'Booking payment',
                'transactionable_type' => $transactionable ? get_class($transactionable) : null,
                'transactionable_id' => $transactionable?->id,
            ]);
        });
    }

    /**
     * Refund funds back to a wallet (e.g. ticket cancellation).
     */
    public function refund(User $user, float $amount, ?Model $transactionable = null, ?string $description = null): WalletTransaction
    {
        if ($amount <= 0) {
            throw new RuntimeException('Refund amount must be greater than zero.');
        }

        return DB::transaction(function () use ($user, $amount, $transactionable, $description) {
            $wallet = $this->walletFor($user);
            $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            $newBalance = bcadd($wallet->balance, $amount, 2);
            $wallet->update(['balance' => $newBalance]);

            return WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'refund',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => 'TXN-' . strtoupper(Str::random(10)),
                'description' => $description ?? 'Booking refund',
                'transactionable_type' => $transactionable ? get_class($transactionable) : null,
                'transactionable_id' => $transactionable?->id,
            ]);
        });
    }
}
