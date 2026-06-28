<?php

namespace App\Models;

use Carbon\Carbon;
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

    /** Masked account number for member-facing read-only views, e.g. ****1234. */
    public function getMaskedAccountNumberAttribute(): string
    {
        $num = preg_replace('/\s+/', '', (string) $this->account_number);
        $last = substr($num, -4);
        return '****' . $last;
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

    // ── Period-aware figures (for the date-range filter) ─────────────────────
    // Flows are summed within [$from, $to]; the available balance is a position
    // taken as of a given date.

    public function depositsBetween(?Carbon $from, ?Carbon $to): float
    {
        return (float) $this->deposits()
            ->when($from, fn($q) => $q->whereDate('deposit_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('deposit_date', '<=', $to))
            ->sum('amount');
    }

    public function withdrawalsBetween(?Carbon $from, ?Carbon $to): float
    {
        return (float) $this->withdrawals()
            ->when($from, fn($q) => $q->whereDate('withdrawal_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('withdrawal_date', '<=', $to))
            ->sum('amount');
    }

    public function fdrInterestBetween(?Carbon $from, ?Carbon $to): float
    {
        return (float) $this->fdrs()
            ->whereIn('status', ['matured', 'closed'])
            ->when($from, fn($q) => $q->whereDate('closure_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('closure_date', '<=', $to))
            ->get(['interest_received', 'tax_deduction'])
            ->sum(fn($f) => max(0, (float) $f->interest_received - (float) $f->tax_deduction));
    }

    /** Principal still locked in FDRs as of a date. */
    public function activeFdrAsOf(?Carbon $asOf = null): float
    {
        $asOf = $asOf ?: Carbon::now();
        return (float) $this->fdrs()
            ->whereDate('opening_date', '<=', $asOf)
            ->where(fn($w) => $w->whereNull('closure_date')->orWhereDate('closure_date', '>', $asOf))
            ->sum('principal_amount');
    }

    /** Available balance position as of a date. */
    public function availableBalanceAsOf(?Carbon $asOf = null): float
    {
        $asOf = $asOf ?: Carbon::now();

        $deposited = $this->depositsBetween(null, $asOf);
        $withdrawn = $this->withdrawalsBetween(null, $asOf);

        $closed = $this->fdrs()
            ->whereIn('status', ['matured', 'closed'])
            ->whereDate('closure_date', '<=', $asOf)
            ->get(['principal_amount', 'principal_returned', 'interest_received', 'tax_deduction']);

        $interest = $closed->sum(fn($f) => max(0, (float) $f->interest_received - (float) $f->tax_deduction));
        $adj      = $closed->sum(fn($f) => (float) ($f->principal_returned ?? $f->principal_amount) - (float) $f->principal_amount);

        return (float) $this->opening_balance
            + $deposited - $withdrawn
            - $this->activeFdrAsOf($asOf)
            + $interest + $adj;
    }
}
