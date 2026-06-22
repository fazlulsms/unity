<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('creator');

        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->from) {
            $query->where('date', '>=', $request->from);
        }

        if ($request->to) {
            $query->where('date', '<=', $request->to);
        }

        $expenses   = $query->orderByDesc('date')->paginate(20);
        $categories = Expense::distinct()->pluck('category');
        $totalActive = Expense::where('status', 'active')->sum('amount');

        return view('admin.expenses.index', compact('expenses', 'categories', 'totalActive'));
    }

    public function create()
    {
        $categories = Expense::distinct()->pluck('category');
        return view('admin.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'           => 'required|date',
            'category'       => 'required|string|max:100',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,bkash,nagad,rocket,other',
            'paid_by'        => 'nullable|string|max:255',
            'description'    => 'required|string|max:1000',
            'attachment'     => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'          => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = $file->hashName();
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir = $base . '/uploads/expenses';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'expenses/' . $filename;
        }

        $data['created_by'] = auth()->id();
        $data['approved_by'] = auth()->id();
        $data['approved_at'] = now();

        $expense = Expense::create($data);

        AuditLog::record('expense_created', $expense, [], $expense->toArray());

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = Expense::distinct()->pluck('category');
        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $old  = $expense->toArray();
        $data = $request->validate([
            'date'           => 'required|date',
            'category'       => 'required|string|max:100',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,bkash,nagad,rocket,other',
            'paid_by'        => 'nullable|string|max:255',
            'description'    => 'required|string|max:1000',
            'attachment'     => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'          => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('attachment')) {
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            if ($expense->attachment) {
                $oldPath = $base . '/uploads/' . $expense->attachment;
                if (file_exists($oldPath)) @unlink($oldPath);
            }
            $file = $request->file('attachment');
            $filename = $file->hashName();
            $dir = $base . '/uploads/expenses';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['attachment'] = 'expenses/' . $filename;
        }

        $data['updated_by'] = auth()->id();
        $expense->update($data);

        AuditLog::record('expense_updated', $expense, $old, $expense->fresh()->toArray());

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense updated.');
    }

    public function void(Expense $expense)
    {
        $old = $expense->toArray();
        $expense->update(['status' => 'voided', 'updated_by' => auth()->id()]);
        AuditLog::record('expense_voided', $expense, $old, ['status' => 'voided']);

        return back()->with('success', 'Expense voided.');
    }
}
