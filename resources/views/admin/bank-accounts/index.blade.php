@extends('layouts.app')
@section('title', 'Bank Accounts')
@section('page-title', 'Bank Accounts')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5 max-w-screen-xl">

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="card p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Deposited</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">৳{{ number_format($totals['deposited'], 0) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Active FDR</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">৳{{ number_format($totals['active_fdr'], 0) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Withdrawn</p>
            <p class="text-2xl font-bold text-red-600 mt-1">৳{{ number_format($totals['withdrawn'], 0) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Available Balance</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">৳{{ number_format($totals['available'], 0) }}</p>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ $accounts->count() }} account(s)</p>
        <a href="{{ route('admin.bank-accounts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">+ Add Bank Account</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Bank / Account</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Account No.</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Deposited</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Active FDR</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Withdrawn</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Available</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($accounts as $account)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-900">{{ $account->bank_name }}</p>
                        <p class="text-xs text-gray-400">{{ $account->account_name }}{{ $account->branch_name ? ' · ' . $account->branch_name : '' }} · {{ $account->account_type_label }}</p>
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $account->account_number }}</td>
                    <td class="px-5 py-3 text-right text-gray-700">৳{{ number_format($account->total_deposited, 0) }}</td>
                    <td class="px-5 py-3 text-right text-purple-600">৳{{ number_format($account->active_fdr_amount, 0) }}</td>
                    <td class="px-5 py-3 text-right text-red-600">৳{{ number_format($account->total_withdrawn, 0) }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-emerald-600">৳{{ number_format($account->available_balance, 0) }}</td>
                    <td class="px-5 py-3">
                        <span class="badge-{{ $account->isActive() ? 'active' : 'inactive' }}">{{ ucfirst($account->status) }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.bank-accounts.show', $account) }}" class="text-gray-500 text-xs hover:underline">View</a>
                            <a href="{{ route('admin.bank-accounts.edit', $account) }}" class="text-blue-600 text-xs hover:underline">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400">No bank accounts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
