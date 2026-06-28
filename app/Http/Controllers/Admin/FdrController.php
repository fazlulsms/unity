<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\FdrRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FdrController extends Controller
{
    public function index()
    {
        $fdrs           = FdrRecord::with('creator', 'linkedIncome')->latest()->paginate(20);
        $totalPrincipal = FdrRecord::whereIn('status', ['active', 'matured'])->sum('principal_amount');
        $totalInterest  = FdrRecord::sum('interest_received');
        $activeFdrCount = FdrRecord::where('status', 'active')->count();
        $closedFdrCount = FdrRecord::whereIn('status', ['matured', 'closed', 'renewed'])->count();

        return view('admin.fdr.index', compact('fdrs', 'totalPrincipal', 'totalInterest', 'activeFdrCount', 'closedFdrCount'));
    }

    public function create()
    {
        $accounts = BankAccount::where('status', 'active')->orderBy('bank_name')->get();
        return view('admin.fdr.create', compact('accounts'));
    }

    public function show(FdrRecord $fdr)
    {
        $fdr->load('linkedIncome', 'creator');
        return view('admin.fdr.show', compact('fdr'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bank_account_id'          => 'nullable|exists:bank_accounts,id',
            'bank_name'                => 'required|string|max:255',
            'branch'                   => 'nullable|string|max:255',
            'fdr_number'               => 'required|string|max:100',
            'opening_date'             => 'required|date',
            'maturity_date'            => 'required|date|after:opening_date',
            'principal_amount'         => 'required|numeric|min:1',
            'interest_rate'            => 'required|numeric|min:0|max:100',
            'expected_maturity_amount' => 'nullable|numeric|min:0',
            'interest_received'        => 'nullable|numeric|min:0',
            'status'                   => 'required|in:active,matured,renewed,closed',
            'is_public_reference'      => 'nullable|boolean',
            'attachment'               => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'                    => 'nullable|string|max:1000',
        ]);

        // When funded from a tracked bank account, principal cannot exceed its available balance.
        if (!empty($data['bank_account_id']) && $data['status'] === 'active') {
            $account   = BankAccount::find($data['bank_account_id']);
            $available = $account?->available_balance ?? 0;
            if ($data['principal_amount'] > $available) {
                throw ValidationException::withMessages([
                    'principal_amount' => 'FDR amount exceeds available balance (৳' . number_format($available, 2) . ') for the selected account.',
                ]);
            }
        }

        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            $filename = $file->hashName();
            $base     = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir      = $base . '/uploads/fdr';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'fdr/' . $filename;
        }

        $data['is_public_reference'] = $request->boolean('is_public_reference');
        $data['created_by']          = auth()->id();

        $fdr = FdrRecord::create($data);
        AuditLog::record('fdr_created', $fdr, [], $fdr->toArray());

        if ($fdr->interest_received) {
            $fdr->syncLinkedIncome(auth()->id());
        }

        return redirect()->route('admin.fdr.index')
            ->with('success', 'FDR record created.');
    }

    public function edit(FdrRecord $fdr)
    {
        if ($fdr->isClosed() && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.fdr.show', $fdr)
                ->with('error', 'A closed FDR can only be edited by a super admin.');
        }
        $accounts = BankAccount::orderBy('bank_name')->get();
        return view('admin.fdr.edit', compact('fdr', 'accounts'));
    }

    public function update(Request $request, FdrRecord $fdr)
    {
        if ($fdr->isClosed() && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.fdr.show', $fdr)
                ->with('error', 'A closed FDR can only be edited by a super admin.');
        }

        $old  = $fdr->toArray();
        $data = $request->validate([
            'bank_account_id'          => 'nullable|exists:bank_accounts,id',
            'bank_name'                => 'required|string|max:255',
            'branch'                   => 'nullable|string|max:255',
            'fdr_number'               => 'required|string|max:100',
            'opening_date'             => 'required|date',
            'maturity_date'            => 'required|date|after:opening_date',
            'principal_amount'         => 'required|numeric|min:1',
            'interest_rate'            => 'required|numeric|min:0|max:100',
            'expected_maturity_amount' => 'nullable|numeric|min:0',
            'interest_received'        => 'nullable|numeric|min:0',
            'status'                   => 'required|in:active,matured,renewed,closed',
            'is_public_reference'      => 'nullable|boolean',
            'attachment'               => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'                    => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('attachment')) {
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            if ($fdr->attachment) {
                $oldPath = $base . '/uploads/' . $fdr->attachment;
                if (file_exists($oldPath)) @unlink($oldPath);
            }
            $file     = $request->file('attachment');
            $filename = $file->hashName();
            $dir      = $base . '/uploads/fdr';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'fdr/' . $filename;
        }

        $data['is_public_reference'] = $request->boolean('is_public_reference');
        $data['updated_by']          = auth()->id();

        $fdr->update($data);
        AuditLog::record('fdr_updated', $fdr, $old, $fdr->fresh()->toArray());

        $fdr->refresh();
        $fdr->syncLinkedIncome(auth()->id());

        return redirect()->route('admin.fdr.index')->with('success', 'FDR record updated.');
    }

    public function closeForm(FdrRecord $fdr)
    {
        if ($fdr->isClosed()) {
            return redirect()->route('admin.fdr.show', $fdr)
                ->with('error', 'This FDR is already closed or matured.');
        }
        return view('admin.fdr.close', compact('fdr'));
    }

    public function close(Request $request, FdrRecord $fdr)
    {
        if ($fdr->isClosed()) {
            return redirect()->route('admin.fdr.show', $fdr)
                ->with('error', 'This FDR is already closed or matured.');
        }

        $data = $request->validate([
            'closure_date'           => 'required|date',
            'principal_returned'     => 'nullable|numeric|min:0',
            'interest_received'      => 'required|numeric|min:0',
            'tax_deduction'          => 'nullable|numeric|min:0',
            'actual_maturity_amount' => 'nullable|numeric|min:0',
            'status'                 => 'required|in:matured,closed,renewed',
            'closure_attachment'     => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'                  => 'nullable|string|max:1000',
        ]);

        // Default the principal returned to the original principal when left blank.
        if (($data['principal_returned'] ?? null) === null) {
            $data['principal_returned'] = $fdr->principal_amount;
        }
        $data['tax_deduction'] = $data['tax_deduction'] ?? 0;

        if ($request->hasFile('closure_attachment')) {
            $file     = $request->file('closure_attachment');
            $filename = $file->hashName();
            $base     = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir      = $base . '/uploads/fdr';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['closure_attachment'] = 'fdr/' . $filename;
        }

        $data['updated_by'] = auth()->id();
        $old = $fdr->toArray();
        $fdr->update($data);
        AuditLog::record('fdr_closed', $fdr, $old, $fdr->fresh()->toArray());

        $fdr->refresh();
        $fdr->syncLinkedIncome(auth()->id());

        return redirect()->route('admin.fdr.show', $fdr)
            ->with('success', 'FDR closed and interest posted to Other Income.');
    }
}
