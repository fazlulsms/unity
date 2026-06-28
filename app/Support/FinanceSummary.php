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

/**
 * Single source of truth for club-wide financial figures so that monthly fees,
 * Booster Contribution, bank, FDR and cash-flow numbers stay consistent across
 * the admin dashboard, bank summary, member dashboard and statements.
 *
 * Booster Contribution is ALWAYS counted as direct member contribution here —
 * never as other income, donation, fine or miscellaneous.
 */
class FinanceSummary
{
    /** Approved monthly fee collection (all time). */
    public static function monthlyCollection(): float
    {
        return (float) MonthlyFeeSubmission::where('status', 'approved')->sum('amount');
    }

    /** Total Booster Contribution collected (all time). */
    public static function boosterCollection(): float
    {
        return (float) BoosterPayment::sum('amount');
    }

    /** Total member contribution = monthly fees + booster. */
    public static function totalMemberContribution(): float
    {
        return self::monthlyCollection() + self::boosterCollection();
    }

    /** Expected monthly fee from active members (this calendar month). */
    public static function monthlyExpectedThisMonth(): float
    {
        return (float) Member::where('status', 'active')->sum('monthly_fee_amount');
    }

    // ── Bank & cash flow ─────────────────────────────────────────────────────

    public static function totalBankDeposits(): float
    {
        return (float) BankDeposit::sum('amount');
    }

    public static function totalWithdrawals(): float
    {
        return (float) BankWithdrawal::sum('amount');
    }

    /**
     * Cash in Hand = total member contribution (cash collected) − bank deposits.
     * Booster collection is part of the money flowing into the club, so it is
     * included in the collection side of the match.
     */
    public static function cashInHand(): float
    {
        return self::totalMemberContribution() - self::totalBankDeposits();
    }

    public static function totalAvailableBankBalance(): float
    {
        return (float) BankAccount::all()->sum('available_balance');
    }

    public static function totalActiveFdr(): float
    {
        return (float) FdrRecord::where('status', 'active')->sum('principal_amount');
    }

    /** Net FDR interest income realised (the FDR-interest rows posted to Income). */
    public static function totalFdrInterest(): float
    {
        return (float) Income::where('income_type', 'fdr_interest')
            ->where('status', 'active')->sum('amount');
    }

    /** Cash + available bank balance + active FDR principal. */
    public static function totalClubAssets(): float
    {
        return self::cashInHand()
            + self::totalAvailableBankBalance()
            + self::totalActiveFdr();
    }

    // ── Income / expense ─────────────────────────────────────────────────────

    public static function totalOtherIncome(): float
    {
        return (float) Income::where('status', 'active')->sum('amount');
    }

    public static function totalExpenses(): float
    {
        return (float) Expense::where('status', 'active')->sum('amount');
    }

    /**
     * One array with every headline figure, ready for blade views.
     */
    public static function all(): array
    {
        $monthly  = self::monthlyCollection();
        $booster  = self::boosterCollection();

        return [
            'monthly_collection'      => $monthly,
            'booster_collection'      => $booster,
            'total_member_contribution' => $monthly + $booster,
            'total_bank_deposits'     => self::totalBankDeposits(),
            'cash_in_hand'            => self::cashInHand(),
            'total_available_balance' => self::totalAvailableBankBalance(),
            'total_active_fdr'        => self::totalActiveFdr(),
            'total_fdr_interest'      => self::totalFdrInterest(),
            'total_withdrawals'       => self::totalWithdrawals(),
            'total_club_assets'       => self::totalClubAssets(),
            'total_other_income'      => self::totalOtherIncome(),
            'total_expenses'          => self::totalExpenses(),
        ];
    }
}
