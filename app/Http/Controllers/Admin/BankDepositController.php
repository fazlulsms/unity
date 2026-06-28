<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\BankDeposit;
use Illuminate\Http\Request;

class BankDepositController extends Controller
{
    public function index(Request $request)
    {
        $query = BankDeposit::with('bankAccount', 'creator');

        if ($request->bank_account_id) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        $deposits = $query->latest('deposit_date')->latest('id')->paginate(25)->withQueryString();
        $accounts = BankAccount::orderBy('bank_name')->get();
        $totalDeposited = BankDeposit::sum('amount');

        return view('admin.bank-deposits.index', compact('deposits', 'accounts', 'totalDeposited'));
    }

    public function create(Request $request)
    {
        $accounts = BankAccount::where('status', 'active')->orderBy('bank_name')->get();
        $selected = $request->bank_account_id;

        return view('admin.bank-deposits.create', compact('accounts', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bank_account_id'  => 'required|exists:bank_accounts,id',
            'deposit_date'     => 'required|date',
            'amount'           => 'required|numeric|min:0.01',
            'source_reference' => 'nullable|string|max:255',
            'remarks'          => 'nullable|string|max:1000',
            'attachment'       => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $data['attachment']  = $this->storeAttachment($request);
        $data['created_by']  = auth()->id();

        $deposit = BankDeposit::create($data);
        AuditLog::record('bank_deposit_created', $deposit, [], $deposit->toArray());

        return redirect()->route('admin.bank-deposits.index')
            ->with('success', 'Bank deposit recorded.');
    }

    public function show(BankDeposit $bankDeposit)
    {
        $bankDeposit->load('bankAccount', 'creator');
        return view('admin.bank-deposits.show', compact('bankDeposit'));
    }

    public function edit(BankDeposit $bankDeposit)
    {
        $accounts = BankAccount::orderBy('bank_name')->get();
        return view('admin.bank-deposits.edit', compact('bankDeposit', 'accounts'));
    }

    public function update(Request $request, BankDeposit $bankDeposit)
    {
        $old  = $bankDeposit->toArray();
        $data = $request->validate([
            'bank_account_id'  => 'required|exists:bank_accounts,id',
            'deposit_date'     => 'required|date',
            'amount'           => 'required|numeric|min:0.01',
            'source_reference' => 'nullable|string|max:255',
            'remarks'          => 'nullable|string|max:1000',
            'attachment'       => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $this->deleteAttachment($bankDeposit->attachment);
            $data['attachment'] = $this->storeAttachment($request);
        }
        $data['updated_by'] = auth()->id();

        $bankDeposit->update($data);
        AuditLog::record('bank_deposit_updated', $bankDeposit, $old, $bankDeposit->fresh()->toArray());

        return redirect()->route('admin.bank-deposits.index')
            ->with('success', 'Bank deposit updated.');
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
