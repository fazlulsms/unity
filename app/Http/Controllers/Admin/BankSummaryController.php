<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Support\FinanceSummary;

class BankSummaryController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::with('creator')->orderBy('bank_name')->get();

        // Cash-flow chain: Collection → Bank Deposits → Cash in Hand
        // Total collection includes Booster Contribution (direct member contribution).
        $monthlyCollection = FinanceSummary::monthlyCollection();
        $boosterCollection = FinanceSummary::boosterCollection();
        $totalCollection   = $monthlyCollection + $boosterCollection;
        $totalDeposited    = FinanceSummary::totalBankDeposits();
        $cashInHand        = $totalCollection - $totalDeposited;

        $totalWithdrawn    = FinanceSummary::totalWithdrawals();
        $totalAvailable    = $accounts->sum('available_balance');
        $totalActiveFdr    = FinanceSummary::totalActiveFdr();
        $totalFdrInterest  = FinanceSummary::totalFdrInterest();

        $summary = compact(
            'monthlyCollection', 'boosterCollection',
            'totalCollection', 'totalDeposited', 'cashInHand',
            'totalWithdrawn', 'totalAvailable', 'totalActiveFdr', 'totalFdrInterest'
        );

        return view('admin.bank-summary.index', compact('accounts', 'summary'));
    }
}
