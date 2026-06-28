<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankDeposit;
use App\Models\BankWithdrawal;
use App\Models\FdrRecord;
use App\Models\Income;
use App\Models\MonthlyFeeSubmission;

class BankSummaryController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::with('creator')->orderBy('bank_name')->get();

        // Cash-flow chain: Collection → Bank Deposits → Cash in Hand
        $totalCollection   = (float) MonthlyFeeSubmission::where('status', 'approved')->sum('amount');
        $totalDeposited    = (float) BankDeposit::sum('amount');
        $cashInHand        = $totalCollection - $totalDeposited;

        $totalWithdrawn    = (float) BankWithdrawal::sum('amount');
        $totalAvailable    = $accounts->sum('available_balance');
        $totalActiveFdr    = (float) FdrRecord::where('status', 'active')->sum('principal_amount');
        $totalFdrInterest  = (float) Income::where('income_type', 'fdr_interest')
            ->where('status', 'active')->sum('amount');

        $summary = compact(
            'totalCollection', 'totalDeposited', 'cashInHand',
            'totalWithdrawn', 'totalAvailable', 'totalActiveFdr', 'totalFdrInterest'
        );

        return view('admin.bank-summary.index', compact('accounts', 'summary'));
    }
}
