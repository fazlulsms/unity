<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FdrRecord;
use Illuminate\Http\Request;

class FdrController extends Controller
{
    public function index()
    {
        $fdrs     = FdrRecord::with('creator')->latest()->paginate(20);
        $totalPrincipal = FdrRecord::whereIn('status', ['active', 'matured'])->sum('principal_amount');
        $totalInterest  = FdrRecord::sum('interest_received');

        return view('admin.fdr.index', compact('fdrs', 'totalPrincipal', 'totalInterest'));
    }

    public function create()
    {
        return view('admin.fdr.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
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
            'notes'                    => 'nullable|string|max:1000',
        ]);

        $data['is_public_reference'] = $request->boolean('is_public_reference');
        $data['created_by']          = auth()->id();

        $fdr = FdrRecord::create($data);
        AuditLog::record('fdr_created', $fdr, [], $fdr->toArray());

        return redirect()->route('admin.fdr.index')
            ->with('success', 'FDR record created.');
    }

    public function edit(FdrRecord $fdr)
    {
        return view('admin.fdr.edit', compact('fdr'));
    }

    public function update(Request $request, FdrRecord $fdr)
    {
        $old  = $fdr->toArray();
        $data = $request->validate([
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
            'notes'                    => 'nullable|string|max:1000',
        ]);

        $data['is_public_reference'] = $request->boolean('is_public_reference');
        $data['updated_by']          = auth()->id();

        $fdr->update($data);
        AuditLog::record('fdr_updated', $fdr, $old, $fdr->fresh()->toArray());

        return redirect()->route('admin.fdr.index')->with('success', 'FDR record updated.');
    }
}
