@extends('layouts.app')
@section('title', 'Bank Summary Report')
@section('page-title', 'Bank & Cash Flow Summary')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-6 max-w-screen-xl">

    {{-- Cash flow chain --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Cash Flow</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('admin.collections.index') }}" class="card p-5 border-t-4 border-blue-500 hover:shadow-md transition-shadow cursor-pointer">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Collection</p>
                <p class="text-2xl font-bold text-gray-900">৳{{ number_format($summary['totalCollection'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-1">Approved member fees</p>
            </a>
            <a href="{{ route('admin.bank-deposits.index') }}" class="card p-5 border-t-4 border-emerald-500 hover:shadow-md transition-shadow cursor-pointer">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Bank Deposits</p>
                <p class="text-2xl font-bold text-emerald-600">৳{{ number_format($summary['totalDeposited'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-1">Deposited to banks</p>
            </a>
            <a href="{{ route('admin.collections.index') }}" class="card p-5 border-t-4 {{ $summary['cashInHand'] < 0 ? 'border-red-400' : 'border-amber-400' }} hover:shadow-md transition-shadow cursor-pointer">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Cash in Hand</p>
                <p class="text-2xl font-bold {{ $summary['cashInHand'] < 0 ? 'text-red-600' : 'text-amber-600' }}">৳{{ number_format($summary['cashInHand'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-1">Collection − Bank deposits</p>
            </a>
        </div>
        @if($summary['cashInHand'] < 0)
        <p class="text-xs text-red-600 mt-2"><i class="fas fa-triangle-exclamation"></i> Bank deposits exceed recorded collections — review entries for unmatched amounts.</p>
        @endif
    </div>

    {{-- Bank position cards --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Bank Position</h2>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            <a href="{{ route('admin.bank-accounts.index') }}" class="card p-5 hover:shadow-md transition-shadow cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-400">Available Balance</p>
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center"><i class="fas fa-wallet text-emerald-600 text-xs"></i></span>
                </div>
                <p class="text-2xl font-bold text-emerald-600">৳{{ number_format($summary['totalAvailable'], 0) }}</p>
            </a>
            <a href="{{ route('admin.fdr.index') }}" class="card p-5 hover:shadow-md transition-shadow cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-400">Active FDR</p>
                    <span class="w-7 h-7 rounded-lg bg-purple-100 flex items-center justify-center"><i class="fas fa-building-columns text-purple-600 text-xs"></i></span>
                </div>
                <p class="text-2xl font-bold text-purple-600">৳{{ number_format($summary['totalActiveFdr'], 0) }}</p>
            </a>
            <a href="{{ route('admin.income.index') }}" class="card p-5 hover:shadow-md transition-shadow cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-400">FDR Interest Income</p>
                    <span class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center"><i class="fas fa-arrow-trend-up text-teal-600 text-xs"></i></span>
                </div>
                <p class="text-2xl font-bold text-teal-600">৳{{ number_format($summary['totalFdrInterest'], 0) }}</p>
            </a>
            <a href="{{ route('admin.bank-withdrawals.index') }}" class="card p-5 hover:shadow-md transition-shadow cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-400">Total Withdrawn</p>
                    <span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center"><i class="fas fa-money-bill-wave text-red-600 text-xs"></i></span>
                </div>
                <p class="text-2xl font-bold text-red-600">৳{{ number_format($summary['totalWithdrawn'], 0) }}</p>
            </a>
        </div>
    </div>

    {{-- Bank-wise table --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Bank-wise Summary</h2>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Bank</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Total Deposited</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Active FDR</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Withdrawn</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Interest Income</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Available Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($accounts as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.bank-accounts.show', $account) }}" class="font-medium text-blue-600 hover:underline">{{ $account->bank_name }}</a>
                            <p class="text-xs text-gray-400 font-mono">{{ $account->account_number }}</p>
                        </td>
                        <td class="px-5 py-3 text-right text-gray-700">৳{{ number_format($account->total_deposited, 2) }}</td>
                        <td class="px-5 py-3 text-right text-purple-600">৳{{ number_format($account->active_fdr_amount, 2) }}</td>
                        <td class="px-5 py-3 text-right text-red-600">৳{{ number_format($account->total_withdrawn, 2) }}</td>
                        <td class="px-5 py-3 text-right text-teal-600">৳{{ number_format($account->fdr_interest_income, 2) }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-emerald-600">৳{{ number_format($account->available_balance, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No bank accounts yet.</td></tr>
                    @endforelse
                </tbody>
                @if($accounts->isNotEmpty())
                <tfoot class="bg-gray-50 border-t border-gray-100">
                    <tr class="font-semibold text-gray-900">
                        <td class="px-5 py-3">Total</td>
                        <td class="px-5 py-3 text-right">৳{{ number_format($summary['totalDeposited'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-purple-600">৳{{ number_format($summary['totalActiveFdr'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-red-600">৳{{ number_format($summary['totalWithdrawn'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-teal-600">৳{{ number_format($summary['totalFdrInterest'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-emerald-600">৳{{ number_format($summary['totalAvailable'], 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
