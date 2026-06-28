<?php

namespace App\Support;

use App\Models\BankAccount;
use App\Models\BankDeposit;
use App\Models\BankWithdrawal;
use App\Models\BoosterPayment;
use App\Models\Expense;
use App\Models\FdrRecord;
use App\Models\Income;
use App\Models\Member;
use App\Models\MonthlyFeeSubmission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Single source of truth for club-wide financial figures so that monthly fees,
 * Booster Contribution, bank, FDR and cash-flow numbers stay consistent across
 * the admin dashboard, bank summary, member dashboard and statements.
 *
 * Every figure can be scoped to a reporting period:
 *   • FLOW figures (collection, deposits, withdrawals, interest, income, expense)
 *     are summed within [$from, $to].
 *   • POSITION figures (cash in hand, available balance, active FDR, club assets)
 *     are taken AS OF the period end ($asOf), since a balance is point-in-time.
 *
 * Passing null for the dates means all-time / current position.
 *
 * Booster Contribution is ALWAYS counted as direct member contribution here —
 * never as other income, donation, fine or miscellaneous.
 */
class FinanceSummary
{
    private static function between(Builder $query, string $column, ?Carbon $from, ?Carbon $to): Builder
    {
        if ($from) {
            $query->whereDate($column, '>=', $from);
        }
        if ($to) {
            $query->whereDate($column, '<=', $to);
        }
        return $query;
    }

    // ── Flows ────────────────────────────────────────────────────────────────

    public static function monthlyCollection(?Carbon $from = null, ?Carbon $to = null): float
    {
        return (float) self::between(
            MonthlyFeeSubmission::where('status', 'approved'),
            'payment_date', $from, $to
        )->sum('amount');
    }

    public static function boosterCollection(?Carbon $from = null, ?Carbon $to = null): float
    {
        return (float) self::between(BoosterPayment::query(), 'payment_date', $from, $to)->sum('amount');
    }

    public static function totalMemberContribution(?Carbon $from = null, ?Carbon $to = null): float
    {
        return self::monthlyCollection($from, $to) + self::boosterCollection($from, $to);
    }

    public static function totalBankDeposits(?Carbon $from = null, ?Carbon $to = null): float
    {
        return (float) self::between(BankDeposit::query(), 'deposit_date', $from, $to)->sum('amount');
    }

    public static function totalWithdrawals(?Carbon $from = null, ?Carbon $to = null): float
    {
        return (float) self::between(BankWithdrawal::query(), 'withdrawal_date', $from, $to)->sum('amount');
    }

    public static function totalFdrInterest(?Carbon $from = null, ?Carbon $to = null): float
    {
        return (float) self::between(
            Income::where('income_type', 'fdr_interest')->where('status', 'active'),
            'date', $from, $to
        )->sum('amount');
    }

    public static function totalOtherIncome(?Carbon $from = null, ?Carbon $to = null): float
    {
        return (float) self::between(Income::where('status', 'active'), 'date', $from, $to)->sum('amount');
    }

    public static function totalExpenses(?Carbon $from = null, ?Carbon $to = null): float
    {
        return (float) self::between(Expense::where('status', 'active'), 'date', $from, $to)->sum('amount');
    }

    /** FDRs created within the period. */
    public static function fdrCreated(?Carbon $from = null, ?Carbon $to = null): array
    {
        $q = self::between(FdrRecord::query(), 'opening_date', $from, $to);
        return ['count' => (clone $q)->count(), 'amount' => (float) $q->sum('principal_amount')];
    }

    /** FDRs closed/matured within the period. */
    public static function fdrClosed(?Carbon $from = null, ?Carbon $to = null): array
    {
        $q = self::between(
            FdrRecord::whereIn('status', ['matured', 'closed', 'renewed']),
            'closure_date', $from, $to
        );
        return ['count' => (clone $q)->count(), 'amount' => (float) $q->sum('principal_amount')];
    }

    // ── Positions (as of a date) ─────────────────────────────────────────────

    public static function totalActiveFdr(?Carbon $asOf = null): float
    {
        $asOf = $asOf ?: Carbon::now();
        return (float) FdrRecord::whereDate('opening_date', '<=', $asOf)
            ->where(fn($w) => $w->whereNull('closure_date')->orWhereDate('closure_date', '>', $asOf))
            ->sum('principal_amount');
    }

    public static function totalAvailableBankBalance(?Carbon $asOf = null): float
    {
        return (float) BankAccount::all()->sum(fn($a) => $a->availableBalanceAsOf($asOf));
    }

    /**
     * Cash in Hand as of a date = all member contribution received up to that
     * date − all bank deposits made up to that date.
     */
    public static function cashInHand(?Carbon $asOf = null): float
    {
        return self::totalMemberContribution(null, $asOf) - self::totalBankDeposits(null, $asOf);
    }

    public static function totalClubAssets(?Carbon $asOf = null): float
    {
        return self::cashInHand($asOf)
            + self::totalAvailableBankBalance($asOf)
            + self::totalActiveFdr($asOf);
    }

    // ── Expected (period-aware) ──────────────────────────────────────────────

    /** Monthly fee expected from active members across the period's months. */
    public static function monthlyExpected(?Carbon $from = null, ?Carbon $to = null): float
    {
        $feePerMonth = (float) Member::where('status', 'active')->sum('monthly_fee_amount');
        $months = ($from && $to)
            ? (int) ($from->copy()->startOfMonth()->diffInMonths($to->copy()->startOfMonth()) + 1)
            : 1;
        return $feePerMonth * max(1, $months);
    }

    // ── Everything, ready for blade ──────────────────────────────────────────

    public static function all(?Carbon $from = null, ?Carbon $to = null): array
    {
        $asOf    = $to ?: Carbon::now();
        $monthly = self::monthlyCollection($from, $to);
        $booster = self::boosterCollection($from, $to);

        return [
            // flows in period
            'monthly_collection'        => $monthly,
            'booster_collection'        => $booster,
            'total_member_contribution' => $monthly + $booster,
            'total_bank_deposits'       => self::totalBankDeposits($from, $to),
            'total_withdrawals'         => self::totalWithdrawals($from, $to),
            'total_fdr_interest'        => self::totalFdrInterest($from, $to),
            'total_other_income'        => self::totalOtherIncome($from, $to),
            'total_expenses'            => self::totalExpenses($from, $to),
            'fdr_created'               => self::fdrCreated($from, $to),
            'fdr_closed'                => self::fdrClosed($from, $to),
            // positions as of period end
            'cash_in_hand'              => self::cashInHand($asOf),
            'total_available_balance'   => self::totalAvailableBankBalance($asOf),
            'total_active_fdr'          => self::totalActiveFdr($asOf),
            'total_club_assets'         => self::totalClubAssets($asOf),
        ];
    }
}
