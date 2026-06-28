@extends('layouts.app')
@section('title', 'Club Finance Statement')
@section('page-title', 'Club Finance Statement')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="space-y-6 max-w-screen-xl">

    <p class="text-sm text-gray-500">Read-only snapshot of the club's financial position · {{ $range->label }}.</p>

    @include('partials.period-filter', ['range' => $range, 'action' => route('member.statements.club-finance'), 'pdf' => route('member.statements.club-finance-pdf')])

    {{-- Contributions & cash flow --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Contributions &amp; Cash Flow</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
            $cards = [
                ['Member Collection', $summary['monthly_collection'], 'text-gray-900'],
                ['Booster Collection', $summary['booster_collection'], 'text-gray-900'],
                ['Total Member Contribution', $summary['total_member_contribution'], 'text-emerald-600'],
                ['Bank Deposits', $summary['total_bank_deposits'], 'text-gray-900'],
                ['Cash in Hand', $summary['cash_in_hand'], $summary['cash_in_hand'] < 0 ? 'text-red-600' : 'text-amber-600'],
                ['Available Bank Balance', $summary['total_available_balance'], 'text-emerald-600'],
                ['Active FDR', $summary['total_active_fdr'], 'text-violet-600'],
                ['FDR Interest Earned', $summary['total_fdr_interest'], 'text-teal-600'],
                ['Other Income', $summary['total_other_income'], 'text-gray-900'],
                ['Total Expenses', $summary['total_expenses'], 'text-red-600'],
                ['Total Withdrawals', $summary['total_withdrawals'], 'text-red-600'],
                ['Total Club Assets', $summary['total_club_assets'], 'text-emerald-600'],
            ];
            @endphp
            @foreach($cards as $c)
            <div class="card p-4">
                <p class="text-xs font-medium text-gray-400">{{ $c[0] }}</p>
                <p class="text-xl font-bold {{ $c[2] }} mt-1">৳{{ number_format($c[1], 0) }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Bank-wise summary --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Bank-wise Summary</h2>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Bank</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Total Deposited</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Available Balance</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Active FDR</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Interest Earned</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Withdrawn</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($bankRows as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('member.finance.bank-show', $r['account']) }}" class="font-medium text-blue-600 hover:underline">{{ $r['account']->bank_name }}</a>
                            <p class="text-xs text-gray-400 font-mono">{{ $r['account']->masked_account_number }}</p>
                        </td>
                        <td class="px-5 py-3 text-right text-gray-700">৳{{ number_format($r['deposited'], 0) }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-emerald-600">৳{{ number_format($r['available'], 0) }}</td>
                        <td class="px-5 py-3 text-right text-violet-600">৳{{ number_format($r['activeFdr'], 0) }}</td>
                        <td class="px-5 py-3 text-right text-teal-600">৳{{ number_format($r['interest'], 0) }}</td>
                        <td class="px-5 py-3 text-right text-red-600">৳{{ number_format($r['withdrawn'], 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No bank accounts.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
