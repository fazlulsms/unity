@extends('layouts.app')
@section('title', 'Bank Summary Report')
@section('page-title', 'Bank & Cash Flow Summary')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-6 max-w-screen-xl">

    @include('partials.period-filter', ['range' => $range, 'action' => route('admin.bank-summary.index')])

    {{-- Cash flow (flows in period · positions as of period end) --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Cash Flow <span class="text-gray-300 normal-case">· {{ $range->label }}</span></h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('admin.collections.index') }}" class="card p-5 border-t-4 border-blue-500 hover:shadow-md transition-shadow cursor-pointer">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Member Contribution</p>
                <p class="text-2xl font-bold text-gray-900">৳{{ number_format($summary['total_member_contribution'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-1">Fees ৳{{ number_format($summary['monthly_collection'], 0) }} + Booster ৳{{ number_format($summary['booster_collection'], 0) }}</p>
            </a>
            <a href="{{ route('admin.bank-deposits.index') }}" class="card p-5 border-t-4 border-emerald-500 hover:shadow-md transition-shadow cursor-pointer">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Bank Deposits</p>
                <p class="text-2xl font-bold text-emerald-600">৳{{ number_format($summary['total_bank_deposits'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-1">Deposited in period</p>
            </a>
            <a href="{{ route('admin.collections.index') }}" class="card p-5 border-t-4 {{ $summary['cash_in_hand'] < 0 ? 'border-red-400' : 'border-amber-400' }} hover:shadow-md transition-shadow cursor-pointer">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Cash in Hand</p>
                <p class="text-2xl font-bold {{ $summary['cash_in_hand'] < 0 ? 'text-red-600' : 'text-amber-600' }}">৳{{ number_format($summary['cash_in_hand'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-1">As of {{ $range->asOf()->format('d M Y') }}</p>
            </a>
        </div>
    </div>

    {{-- Bank position --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Bank Position</h2>
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            <a href="{{ route('admin.bank-accounts.index') }}" class="card p-5 hover:shadow-md transition-shadow cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-400">Available Balance</p>
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center"><i class="fas fa-wallet text-emerald-600 text-xs"></i></span>
                </div>
                <p class="text-2xl font-bold text-emerald-600">৳{{ number_format($summary['total_available_balance'], 0) }}</p>
            </a>
            <a href="{{ route('admin.fdr.index') }}" class="card p-5 hover:shadow-md transition-shadow cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-400">Active FDR</p>
                    <span class="w-7 h-7 rounded-lg bg-purple-100 flex items-center justify-center"><i class="fas fa-building-columns text-purple-600 text-xs"></i></span>
                </div>
                <p class="text-2xl font-bold text-purple-600">৳{{ number_format($summary['total_active_fdr'], 0) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $summary['fdr_created']['count'] }} opened · {{ $summary['fdr_closed']['count'] }} closed in period</p>
            </a>
            <a href="{{ route('admin.income.index') }}" class="card p-5 hover:shadow-md transition-shadow cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-400">FDR Interest Income</p>
                    <span class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center"><i class="fas fa-arrow-trend-up text-teal-600 text-xs"></i></span>
                </div>
                <p class="text-2xl font-bold text-teal-600">৳{{ number_format($summary['total_fdr_interest'], 0) }}</p>
            </a>
            <a href="{{ route('admin.bank-withdrawals.index') }}" class="card p-5 hover:shadow-md transition-shadow cursor-pointer">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-medium text-gray-400">Total Withdrawn</p>
                    <span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center"><i class="fas fa-money-bill-wave text-red-600 text-xs"></i></span>
                </div>
                <p class="text-2xl font-bold text-red-600">৳{{ number_format($summary['total_withdrawals'], 0) }}</p>
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
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Deposited</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Active FDR</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Withdrawn</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Interest</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Available Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($bankRows as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.bank-accounts.show', $r['account']) }}" class="font-medium text-blue-600 hover:underline">{{ $r['account']->bank_name }}</a>
                            <p class="text-xs text-gray-400 font-mono">{{ $r['account']->account_number }}</p>
                        </td>
                        <td class="px-5 py-3 text-right text-gray-700">৳{{ number_format($r['deposited'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-purple-600">৳{{ number_format($r['activeFdr'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-red-600">৳{{ number_format($r['withdrawn'], 2) }}</td>
                        <td class="px-5 py-3 text-right text-teal-600">৳{{ number_format($r['interest'], 2) }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-emerald-600">৳{{ number_format($r['available'], 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No bank accounts yet.</td></tr>
                    @endforelse
                </tbody>
                @if($bankRows->isNotEmpty())
                <tfoot class="bg-gray-50 border-t border-gray-100">
                    <tr class="font-semibold text-gray-900">
                        <td class="px-5 py-3">Total</td>
                        <td class="px-5 py-3 text-right">৳{{ number_format($bankRows->sum('deposited'), 2) }}</td>
                        <td class="px-5 py-3 text-right text-purple-600">৳{{ number_format($bankRows->sum('activeFdr'), 2) }}</td>
                        <td class="px-5 py-3 text-right text-red-600">৳{{ number_format($bankRows->sum('withdrawn'), 2) }}</td>
                        <td class="px-5 py-3 text-right text-teal-600">৳{{ number_format($bankRows->sum('interest'), 2) }}</td>
                        <td class="px-5 py-3 text-right text-emerald-600">৳{{ number_format($bankRows->sum('available'), 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
