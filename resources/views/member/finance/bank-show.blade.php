@extends('layouts.app')
@section('title', $bankAccount->bank_name)
@section('page-title', 'Bank Summary')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="space-y-5 max-w-screen-lg">

    {{-- Header --}}
    <div class="card p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                <i class="fas fa-building-columns text-blue-600 text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $bankAccount->bank_name }}</h2>
                <p class="text-sm text-gray-500">{{ $bankAccount->account_name }} · {{ $bankAccount->account_type_label }}</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    A/C <span class="font-mono">{{ $bankAccount->masked_account_number }}</span>
                    {{ $bankAccount->branch_name ? ' · ' . $bankAccount->branch_name . ' branch' : '' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-5"><p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Deposited</p><p class="text-2xl font-bold text-gray-900 mt-1">৳{{ number_format($bankAccount->total_deposited, 0) }}</p></div>
        <div class="card p-5"><p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Available Balance</p><p class="text-2xl font-bold text-emerald-600 mt-1">৳{{ number_format($bankAccount->available_balance, 0) }}</p></div>
        <div class="card p-5"><p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Active FDR</p><p class="text-2xl font-bold text-violet-600 mt-1">৳{{ number_format($bankAccount->active_fdr_amount, 0) }}</p></div>
        <div class="card p-5"><p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Interest Earned</p><p class="text-2xl font-bold text-teal-600 mt-1">৳{{ number_format($bankAccount->fdr_interest_income, 0) }}</p></div>
    </div>

    {{-- Active FDRs --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
        <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-700">Active FDRs</h3></div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">FDR No.</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Principal</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Rate</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Opened</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Maturity</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($activeFdrs as $f)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $f->fdr_number }}</td>
                    <td class="px-5 py-3 text-right font-medium text-gray-900">৳{{ number_format($f->principal_amount, 0) }}</td>
                    <td class="px-5 py-3 text-right text-gray-600">{{ $f->interest_rate }}%</td>
                    <td class="px-5 py-3 text-gray-500">{{ $f->opening_date->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $f->maturity_date->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No active FDRs.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Closed FDR history --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
        <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-700">Closed FDR History</h3></div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">FDR No.</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Principal</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Interest (net)</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Closed</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($closedFdrs as $f)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $f->fdr_number }}</td>
                    <td class="px-5 py-3 text-right text-gray-900">৳{{ number_format($f->principal_amount, 0) }}</td>
                    <td class="px-5 py-3 text-right text-teal-600">৳{{ number_format(max(0, $f->interest_received - $f->tax_deduction), 0) }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $f->closure_date?->format('d M Y') ?? '—' }}</td>
                    <td class="px-5 py-3">@php $c = ['matured'=>'approved','closed'=>'voided'][$f->status] ?? 'closed'; @endphp<span class="badge-{{ $c }}">{{ ucfirst($f->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No closed FDRs.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Withdrawal history (amounts + purpose only) --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
        <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-700">Withdrawal History</h3></div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Date</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Purpose</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($withdrawals as $w)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-500">{{ $w->withdrawal_date->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $w->purpose ?: '—' }}</td>
                    <td class="px-5 py-3 text-right font-medium text-red-600">৳{{ number_format($w->amount, 0) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">No withdrawals.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-xs text-gray-400"><i class="fas fa-lock"></i> Read-only view. Account numbers are masked and confidential documents are not shown.</p>
    <a href="{{ route('member.statements.club-finance') }}" class="text-sm text-gray-500 hover:underline">&larr; Back to club finance</a>
</div>
@endsection
