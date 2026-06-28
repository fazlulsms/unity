<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Support\DateRange;
use App\Support\FinanceSummary;
use Illuminate\Http\Request;

class BankSummaryController extends Controller
{
    public function index(Request $request)
    {
        $range = DateRange::fromRequest($request, 'all');
        $from  = $range->from;
        $to    = $range->to;
        $asOf  = $range->asOf();

        $summary = FinanceSummary::all($from, $to);

        // Bank-wise figures: deposits / withdrawals / interest are period flows;
        // available balance and active FDR are positions as of the period end.
        $accounts = BankAccount::orderBy('bank_name')->get();
        $bankRows = $accounts->map(fn($a) => [
            'account'   => $a,
            'deposited' => $a->depositsBetween($from, $to),
            'available' => $a->availableBalanceAsOf($asOf),
            'activeFdr' => $a->activeFdrAsOf($asOf),
            'interest'  => $a->fdrInterestBetween($from, $to),
            'withdrawn' => $a->withdrawalsBetween($from, $to),
        ]);

        return view('admin.bank-summary.index', compact('range', 'summary', 'bankRows'));
    }
}
