<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\BankWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BankWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = BankWithdrawal::with('bankAccount', 'creator');

        if ($request->bank_account_id) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        $withdrawals = $query->latest('withdrawal_date')->latest('id')->paginate(25)->withQueryString();
        $accounts = BankAccount::orderBy('bank_name')->get();
        $totalWithdrawn = BankWithdrawal::sum('amount');

        return view('admin.bank-withdrawals.index', compact('withdrawals', 'accounts', 'totalWithdrawn'));
    }

    public function create(Request $request)
    {
        $accounts = BankAccount::where('status', 'active')->orderBy('bank_name')->get();
        $selected = $request->bank_account_id;

        return view('admin.bank-withdrawals.create', compact('accounts', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'withdrawal_date' => 'required|date',
            'amount'          => 'required|numeric|min:0.01',
            'cheque_number'   => 'nullable|string|max:100',
            'purpose'         => 'nullable|string|max:255',
            'remarks'         => 'nullable|string|max:1000',
            'attachment'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $account   = BankAccount::findOrFail($data['bank_account_id']);
        $available = $account->available_balance;

        if ($data['amount'] > $available) {
            throw ValidationException::withMessages([
                'amount' => 'Withdrawal exceeds available balance (৳' . number_format($available, 2) . ') for this account.',
            ]);
        }

        $data['attachment'] = $this->storeAttachment($request);
        $data['created_by'] = auth()->id();

        $withdrawal = BankWithdrawal::create($data);
        AuditLog::record('bank_withdrawal_created', $withdrawal, [], $withdrawal->toArray());

        return redirect()->route('admin.bank-withdrawals.index')
            ->with('success', 'Bank withdrawal recorded.');
    }

    public function show(BankWithdrawal $bankWithdrawal)
    {
        $bankWithdrawal->load('bankAccount', 'creator');
        return view('admin.bank-withdrawals.show', compact('bankWithdrawal'));
    }

    public function edit(BankWithdrawal $bankWithdrawal)
    {
        $accounts = BankAccount::orderBy('bank_name')->get();
        return view('admin.bank-withdrawals.edit', compact('bankWithdrawal', 'accounts'));
    }

    public function update(Request $request, BankWithdrawal $bankWithdrawal)
    {
        $old  = $bankWithdrawal->toArray();
        $data = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'withdrawal_date' => 'required|date',
            'amount'          => 'required|numeric|min:0.01',
            'cheque_number'   => 'nullable|string|max:100',
            'purpose'         => 'nullable|string|max:255',
            'remarks'         => 'nullable|string|max:1000',
            'attachment'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $account   = BankAccount::findOrFail($data['bank_account_id']);
        $available = $account->available_balance;
        // The current withdrawal already reduces this account's available balance,
        // so add it back when validating an edit on the same account.
        if ((int) $bankWithdrawal->bank_account_id === (int) $account->id) {
            $available += (float) $bankWithdrawal->amount;
        }

        if ($data['amount'] > $available) {
            throw ValidationException::withMessages([
                'amount' => 'Withdrawal exceeds available balance (৳' . number_format($available, 2) . ') for this account.',
            ]);
        }

        if ($request->hasFile('attachment')) {
            $this->deleteAttachment($bankWithdrawal->attachment);
            $data['attachment'] = $this->storeAttachment($request);
        }
        $data['updated_by'] = auth()->id();

        $bankWithdrawal->update($data);
        AuditLog::record('bank_withdrawal_updated', $bankWithdrawal, $old, $bankWithdrawal->fresh()->toArray());

        return redirect()->route('admin.bank-withdrawals.index')
            ->with('success', 'Bank withdrawal updated.');
    }

    // ── Attachment helpers ───────────────────────────────────────────────────

    private function storeAttachment(Request $request): ?string
    {
        if (!$request->hasFile('attachment')) {
            return null;
        }
        $file     = $request->file('attachment');
        $filename = $file->hashName();
        $base     = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
        $dir      = $base . '/uploads/bank';
        @mkdir($dir, 0755, true);
        $file->move($dir, $filename);

        return 'bank/' . $filename;
    }

    private function deleteAttachment(?string $path): void
    {
        if (!$path) {
            return;
        }
        $base    = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
        $oldPath = $base . '/uploads/' . $path;
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
    }
}
