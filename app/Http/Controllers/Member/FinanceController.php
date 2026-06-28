<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;

/**
 * Read-only financial transparency for members. No create/edit/delete.
 * Confidential data (full account numbers, cheque numbers, deposit slips and
 * other documents) is never exposed here.
 */
class FinanceController extends Controller
{
    public function bankShow(BankAccount $bankAccount)
    {
        // Active FDRs (read-only list) and closed FDR history for this account.
        $activeFdrs = $bankAccount->fdrs()
            ->whereIn('status', ['active', 'renewed'])
            ->orderBy('maturity_date')
            ->get(['id', 'fdr_number', 'principal_amount', 'interest_rate', 'opening_date', 'maturity_date', 'status']);

        $closedFdrs = $bankAccount->fdrs()
            ->whereIn('status', ['matured', 'closed'])
            ->orderByDesc('closure_date')
            ->get(['id', 'fdr_number', 'principal_amount', 'interest_received', 'tax_deduction', 'opening_date', 'closure_date', 'status']);

        // Withdrawal history — amounts and dates only, no cheque numbers / attachments.
        $withdrawals = $bankAccount->withdrawals()
            ->latest('withdrawal_date')->latest('id')
            ->get(['id', 'withdrawal_date', 'amount', 'purpose']);

        return view('member.finance.bank-show', compact('bankAccount', 'activeFdrs', 'closedFdrs', 'withdrawals'));
    }
}
