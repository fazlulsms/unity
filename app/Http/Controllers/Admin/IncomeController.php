<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FdrRecord;
use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Income::with('creator', 'fdr');

        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }

        if ($request->type) {
            $query->where('income_type', $request->type);
        }

        $incomes     = $query->orderByDesc('date')->paginate(20);
        $totalActive = Income::where('status', 'active')->sum('amount');

        return view('admin.income.index', compact('incomes', 'totalActive'));
    }

    public function create()
    {
        $fdrs = FdrRecord::orderBy('bank_name')->get();
        return view('admin.income.create', compact('fdrs'));
    }

    public function store(Request $request)
    {
        $rules = [
            'date'        => 'required|date',
            'income_type' => 'required|in:fdr_interest,donation,extra_contribution,other',
            'source'      => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'reference'   => 'nullable|string|max:100',
            'attachment'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'       => 'nullable|string|max:500',
        ];

        if ($request->income_type === 'fdr_interest') {
            $rules['fdr_id'] = 'required|exists:fdr_records,id';
        }

        $data = $request->validate($rules);

        if ($request->income_type === 'fdr_interest' && !empty($data['fdr_id'])) {
            $existing = Income::where('fdr_id', $data['fdr_id'])
                ->where('status', 'active')
                ->first();
            if ($existing) {
                return back()->withInput()
                    ->withErrors(['fdr_id' => 'This FDR already has a linked income entry. Edit or void the existing entry first.']);
            }
        }

        if ($request->hasFile('attachment')) {
            $file     = $request->file('attachment');
            $filename = $file->hashName();
            $base     = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir      = $base . '/uploads/income';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'income/' . $filename;
        }

        $data['source_module'] = 'manual';
        $data['created_by']    = auth()->id();
        $income = Income::create($data);

        if ($income->income_type === 'fdr_interest' && $income->fdr_id) {
            FdrRecord::where('id', $income->fdr_id)
                ->update(['interest_received' => $income->amount, 'updated_by' => auth()->id()]);
        }

        AuditLog::record('income_created', $income, [], $income->toArray());

        return redirect()->route('admin.income.index')
            ->with('success', 'Income recorded successfully.');
    }

    public function show(Income $income)
    {
        $income->load('fdr');
        return view('admin.income.show', compact('income'));
    }

    public function edit(Income $income)
    {
        $fdrs = FdrRecord::orderBy('bank_name')->get();
        return view('admin.income.edit', compact('income', 'fdrs'));
    }

    public function update(Request $request, Income $income)
    {
        $rules = [
            'date'        => 'required|date',
            'income_type' => 'required|in:fdr_interest,donation,extra_contribution,other',
            'source'      => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'reference'   => 'nullable|string|max:100',
            'attachment'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'       => 'nullable|string|max:500',
        ];

        if ($request->income_type === 'fdr_interest') {
            $rules['fdr_id'] = 'required|exists:fdr_records,id';
        }

        $old  = $income->toArray();
        $data = $request->validate($rules);

        if ($request->income_type === 'fdr_interest' && !empty($data['fdr_id']) && $data['fdr_id'] != $income->fdr_id) {
            $existing = Income::where('fdr_id', $data['fdr_id'])
                ->where('status', 'active')
                ->where('id', '!=', $income->id)
                ->first();
            if ($existing) {
                return back()->withInput()
                    ->withErrors(['fdr_id' => 'This FDR already has a linked income entry. Edit or void it first.']);
            }
        }

        if ($request->hasFile('attachment')) {
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            if ($income->attachment) {
                $oldPath = $base . '/uploads/' . $income->attachment;
                if (file_exists($oldPath)) @unlink($oldPath);
            }
            $file     = $request->file('attachment');
            $filename = $file->hashName();
            $dir      = $base . '/uploads/income';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'income/' . $filename;
        }

        $data['updated_by'] = auth()->id();
        $income->update($data);
        AuditLog::record('income_updated', $income, $old, $income->fresh()->toArray());

        if ($income->income_type === 'fdr_interest' && $income->fdr_id) {
            FdrRecord::where('id', $income->fdr_id)
                ->update(['interest_received' => $income->amount, 'updated_by' => auth()->id()]);
        }

        return redirect()->route('admin.income.index')->with('success', 'Income updated.');
    }

    public function void(Income $income)
    {
        $old = $income->toArray();
        $income->update(['status' => 'voided', 'updated_by' => auth()->id()]);
        AuditLog::record('income_voided', $income, $old, ['status' => 'voided']);

        if ($income->income_type === 'fdr_interest' && $income->fdr_id && $income->source_module === 'manual') {
            FdrRecord::where('id', $income->fdr_id)
                ->update(['interest_received' => 0, 'updated_by' => auth()->id()]);
        }

        return back()->with('success', 'Income entry voided.');
    }
}
