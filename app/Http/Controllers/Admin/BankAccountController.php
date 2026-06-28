<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::with('creator')->latest()->get();

        $totals = [
            'deposited'  => $accounts->sum('total_deposited'),
            'withdrawn'  => $accounts->sum('total_withdrawn'),
            'active_fdr' => $accounts->sum('active_fdr_amount'),
            'available'  => $accounts->sum('available_balance'),
        ];

        return view('admin.bank-accounts.index', compact('accounts', 'totals'));
    }

    public function create()
    {
        return view('admin.bank-accounts.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['created_by'] = auth()->id();

        $account = BankAccount::create($data);
        AuditLog::record('bank_account_created', $account, [], $account->toArray());

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Bank account added.');
    }

    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load('creator', 'fdrs');

        $deposits    = $bankAccount->deposits()->latest('deposit_date')->latest('id')->limit(15)->get();
        $withdrawals = $bankAccount->withdrawals()->latest('withdrawal_date')->latest('id')->limit(15)->get();

        return view('admin.bank-accounts.show', compact('bankAccount', 'deposits', 'withdrawals'));
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('admin.bank-accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $old  = $bankAccount->toArray();
        $data = $this->validateData($request);
        $data['updated_by'] = auth()->id();

        $bankAccount->update($data);
        AuditLog::record('bank_account_updated', $bankAccount, $old, $bankAccount->fresh()->toArray());

        return redirect()->route('admin.bank-accounts.show', $bankAccount)
            ->with('success', 'Bank account updated.');
    }

    public function toggleStatus(BankAccount $bankAccount)
    {
        $old = $bankAccount->status;
        $bankAccount->update([
            'status'     => $bankAccount->isActive() ? 'inactive' : 'active',
            'updated_by' => auth()->id(),
        ]);
        AuditLog::record('bank_account_status_changed', $bankAccount,
            ['status' => $old], ['status' => $bankAccount->status]);

        return back()->with('success', 'Bank account marked ' . $bankAccount->status . '.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'bank_name'       => 'required|string|max:255',
            'branch_name'     => 'nullable|string|max:255',
            'account_name'    => 'required|string|max:255',
            'account_number'  => 'required|string|max:100',
            'account_type'    => 'required|in:savings,current,fixed,other',
            'opening_balance' => 'required|numeric|min:0',
            'status'          => 'required|in:active,inactive',
            'notes'           => 'nullable|string|max:1000',
        ]);
    }
}
