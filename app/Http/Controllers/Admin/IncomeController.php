<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Income::with('creator');

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
        return view('admin.income.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'        => 'required|date',
            'income_type' => 'required|in:fdr_interest,donation,extra_contribution,other',
            'source'      => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'reference'   => 'nullable|string|max:100',
            'attachment'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'       => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $file->hashName();
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir = $base . '/uploads/income';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'income/' . $filename;
        }

        $data['created_by'] = auth()->id();
        $income = Income::create($data);

        AuditLog::record('income_created', $income, [], $income->toArray());

        return redirect()->route('admin.income.index')
            ->with('success', 'Income recorded successfully.');
    }

    public function edit(Income $income)
    {
        return view('admin.income.edit', compact('income'));
    }

    public function update(Request $request, Income $income)
    {
        $old  = $income->toArray();
        $data = $request->validate([
            'date'        => 'required|date',
            'income_type' => 'required|in:fdr_interest,donation,extra_contribution,other',
            'source'      => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0.01',
            'reference'   => 'nullable|string|max:100',
            'attachment'  => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'       => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('attachment')) {
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            if ($income->attachment) {
                $oldPath = $base . '/uploads/' . $income->attachment;
                if (file_exists($oldPath)) @unlink($oldPath);
            }
            $file = $request->file('attachment');
            $filename = $file->hashName();
            $dir = $base . '/uploads/income';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'income/' . $filename;
        }

        $data['updated_by'] = auth()->id();
        $income->update($data);

        AuditLog::record('income_updated', $income, $old, $income->fresh()->toArray());

        return redirect()->route('admin.income.index')->with('success', 'Income updated.');
    }

    public function void(Income $income)
    {
        $old = $income->toArray();
        $income->update(['status' => 'voided', 'updated_by' => auth()->id()]);
        AuditLog::record('income_voided', $income, $old, ['status' => 'voided']);

        return back()->with('success', 'Income entry voided.');
    }
}
