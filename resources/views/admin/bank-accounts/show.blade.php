@extends('layouts.app')
@section('title', 'Bank Account')
@section('page-title', 'Bank Account Detail')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5 max-w-screen-xl">

    {{-- Header --}}
    <div class="card p-6 flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                <i class="fas fa-piggy-bank text-blue-600 text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $bankAccount->bank_name }}</h2>
                <p class="text-sm text-gray-500">{{ $bankAccount->account_name }} · {{ $bankAccount->account_type_label }}</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    A/C <span class="font-mono">{{ $bankAccount->account_number }}</span>
                    {{ $bankAccount->branch_name ? ' · ' . $bankAccount->branch_name . ' branch' : '' }}
                </p>
                <span class="badge-{{ $bankAccount->isActive() ? 'active' : 'inactive' }} mt-2 inline-block">{{ ucfirst($bankAccount->status) }}</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.bank-deposits.create', ['bank_account_id' => $bankAccount->id]) }}" class="text-xs bg-emerald-600 text-white px-3 py-2 rounded-lg hover:bg-emerald-700">+ Deposit</a>
            <a href="{{ route('admin.bank-withdrawals.create', ['bank_account_id' => $bankAccount->id]) }}" class="text-xs bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700">- Withdraw</a>
            <a href="{{ route('admin.fdr.create') }}" class="text-xs bg-purple-600 text-white px-3 py-2 rounded-lg hover:bg-purple-700">+ FDR</a>
            <a href="{{ route('admin.bank-accounts.edit', $bankAccount) }}" class="text-xs border border-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50">Edit</a>
            <form action="{{ route('admin.bank-accounts.toggle-status', $bankAccount) }}" method="POST" onsubmit="return confirm('Change account status?')">
                @csrf
                <button type="submit" class="text-xs border border-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50">
                    {{ $bankAccount->isActive() ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Balance breakdown --}}
    <div class="grid lg:grid-cols-3 gap-5">
        <div class="card p-6 lg:col-span-1">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Balance Breakdown</h3>
            <dl class="space-y-2.5 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Opening Balance</dt><dd class="font-medium text-gray-900">৳{{ number_format($bankAccount->opening_balance, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">+ Total Deposited</dt><dd class="font-medium text-gray-900">৳{{ number_format($bankAccount->total_deposited, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">− Total Withdrawn</dt><dd class="font-medium text-red-600">৳{{ number_format($bankAccount->total_withdrawn, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">− Active FDR (locked)</dt><dd class="font-medium text-purple-600">৳{{ number_format($bankAccount->active_fdr_amount, 2) }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">+ FDR Interest Income (net)</dt><dd class="font-medium text-teal-600">৳{{ number_format($bankAccount->fdr_interest_income, 2) }}</dd></div>
                @if(abs($bankAccount->fdr_principal_adjustment) >= 0.01)
                <div class="flex justify-between"><dt class="text-gray-500">± FDR Principal Adjustment</dt><dd class="font-medium {{ $bankAccount->fdr_principal_adjustment < 0 ? 'text-red-600' : 'text-gray-900' }}">৳{{ number_format($bankAccount->fdr_principal_adjustment, 2) }}</dd></div>
                @endif
                <div class="flex justify-between pt-2.5 border-t border-gray-100"><dt class="font-semibold text-gray-700">Available Balance</dt><dd class="font-bold text-emerald-600 text-base">৳{{ number_format($bankAccount->available_balance, 2) }}</dd></div>
            </dl>
            @if($bankAccount->notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Notes</p>
                <p class="text-sm text-gray-600">{{ $bankAccount->notes }}</p>
            </div>
            @endif
        </div>

        <div class="lg:col-span-2 space-y-5">
            {{-- Recent deposits --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Recent Deposits</h3>
                    <a href="{{ route('admin.bank-deposits.index', ['bank_account_id' => $bankAccount->id]) }}" class="text-xs text-blue-600 hover:underline">View all</a>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-50">
                        @forelse($deposits as $d)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-2.5 text-gray-500">{{ $d->deposit_date->format('d M Y') }}</td>
                            <td class="px-5 py-2.5 text-gray-600">{{ $d->source_reference ?: '—' }}</td>
                            <td class="px-5 py-2.5 text-right font-medium text-emerald-600">+ ৳{{ number_format($d->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td class="px-5 py-6 text-center text-gray-400">No deposits.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Recent withdrawals --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Recent Withdrawals</h3>
                    <a href="{{ route('admin.bank-withdrawals.index', ['bank_account_id' => $bankAccount->id]) }}" class="text-xs text-blue-600 hover:underline">View all</a>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-50">
                        @forelse($withdrawals as $w)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-2.5 text-gray-500">{{ $w->withdrawal_date->format('d M Y') }}</td>
                            <td class="px-5 py-2.5 text-gray-600">{{ $w->purpose ?: '—' }}</td>
                            <td class="px-5 py-2.5 text-right font-medium text-red-600">− ৳{{ number_format($w->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td class="px-5 py-6 text-center text-gray-400">No withdrawals.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- FDRs --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700">FDRs from this account</h3>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-50">
                        @forelse($bankAccount->fdrs as $f)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-2.5 font-mono text-xs text-gray-600">{{ $f->fdr_number }}</td>
                            <td class="px-5 py-2.5 text-right font-medium text-gray-900">৳{{ number_format($f->principal_amount, 2) }}</td>
                            <td class="px-5 py-2.5">
                                @php $c = ['active'=>'active','matured'=>'approved','renewed'=>'pending','closed'=>'voided'][$f->status] ?? 'voided'; @endphp
                                <span class="badge-{{ $c }}">{{ ucfirst($f->status) }}</span>
                            </td>
                            <td class="px-5 py-2.5 text-right"><a href="{{ route('admin.fdr.show', $f) }}" class="text-xs text-blue-600 hover:underline">View</a></td>
                        </tr>
                        @empty
                        <tr><td class="px-5 py-6 text-center text-gray-400">No FDRs from this account.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
