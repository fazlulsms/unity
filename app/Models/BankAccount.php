<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'bank_name', 'branch_name', 'account_name', 'account_number',
        'account_type', 'opening_balance', 'status', 'notes',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deposits()
    {
        return $this->hasMany(BankDeposit::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(BankWithdrawal::class);
    }

    public function fdrs()
    {
        return $this->hasMany(FdrRecord::class, 'bank_account_id');
    }

    // ── Status helpers ───────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getAccountTypeLabelAttribute(): string
    {
        return match ($this->account_type) {
            'savings' => 'Savings',
            'current' => 'Current',
            'fixed'   => 'Fixed Deposit',
            default   => 'Other',
        };
    }

    // ── Money flow accessors ─────────────────────────────────────────────────
    // All figures are derived only from actual deposit / withdrawal / FDR entries.

    public function getTotalDepositedAttribute(): float
    {
        return (float) $this->deposits()->sum('amount');
    }

    public function getTotalWithdrawnAttribute(): float
    {
        return (float) $this->withdrawals()->sum('amount');
    }

    /**
     * Principal currently locked away in FDRs (still running or rolled over).
     */
    public function getActiveFdrAmountAttribute(): float
    {
        return (float) $this->fdrs()->whereIn('status', ['active', 'renewed'])->sum('principal_amount');
    }

    /**
     * Net FDR interest income realised on this account (closed / matured FDRs).
     */
    public function getFdrInterestIncomeAttribute(): float
    {
        return (float) $this->fdrs()
            ->whereIn('status', ['matured', 'closed'])
            ->get(['interest_received', 'tax_deduction'])
            ->sum(fn($f) => max(0, (float) $f->interest_received - (float) $f->tax_deduction));
    }

    /**
     * Gain/loss on principal when an FDR is closed for more or less than its
     * original principal (normally zero — the full principal is returned).
     */
    public function getFdrPrincipalAdjustmentAttribute(): float
    {
        return (float) $this->fdrs()
            ->whereIn('status', ['matured', 'closed'])
            ->get(['principal_amount', 'principal_returned'])
            ->sum(fn($f) => (float) ($f->principal_returned ?? $f->principal_amount) - (float) $f->principal_amount);
    }

    /**
     * Available Bank Balance =
     *   Opening Balance
     * + Total Deposited            (every taka ever put into the bank)
     * - Total Withdrawn
     * - Active FDR principal        (currently locked in FDRs)
     * + Realised FDR interest       (the only new money from a closed FDR)
     * + Principal adjustment        (any closure gain/loss on principal)
     *
     * The principal that funded a closed FDR is already part of Total Deposited,
     * so only the interest (and any principal gain/loss) is added back on closure.
     */
    public function getAvailableBalanceAttribute(): float
    {
        return (float) $this->opening_balance
            + $this->total_deposited
            - $this->total_withdrawn
            - $this->active_fdr_amount
            + $this->fdr_interest_income
            + $this->fdr_principal_adjustment;
    }
}
