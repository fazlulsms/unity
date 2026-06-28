@extends('layouts.app')
@section('title', 'Club Finances')
@section('page-title', 'Club Transparency Dashboard')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="max-w-screen-xl space-y-6">

    @include('partials.period-filter', ['range' => $range, 'action' => route('member.transparency'), 'pdf' => route('member.statements.club-finance-pdf')])

    {{-- ── Headline stats ─────────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0"><i class="fas fa-users text-blue-600 text-lg"></i></div>
            <div><p class="text-2xl font-bold text-gray-900">{{ $activeMembers }}</p><p class="text-xs text-gray-500 font-medium mt-0.5">Active Members</p></div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0"><i class="fas fa-vault text-emerald-600 text-lg"></i></div>
            <div><p class="text-2xl font-bold text-emerald-600">৳{{ number_format($finance['total_club_assets'], 0) }}</p><p class="text-xs text-gray-500 font-medium mt-0.5">Total Club Assets</p></div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center shrink-0"><i class="fas fa-building-columns text-violet-600 text-lg"></i></div>
            <div><p class="text-2xl font-bold text-violet-600">৳{{ number_format($finance['total_active_fdr'], 0) }}</p><p class="text-xs text-gray-500 font-medium mt-0.5">FDR Investment</p></div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0"><i class="fas fa-chart-pie text-amber-600 text-lg"></i></div>
            <div><p class="text-2xl font-bold text-amber-600">{{ $collectionPercent }}%</p><p class="text-xs text-gray-500 font-medium mt-0.5">Collected vs Expected</p></div>
        </div>
    </div>

    {{-- ── Collection Overview ────────────────────────────── --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Collection Overview <span class="text-gray-300 normal-case">· {{ $range->label }}</span></h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card p-5 border-t-4 border-indigo-500">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Expected</p>
                <p class="text-2xl font-bold text-gray-900">৳{{ number_format($expected, 0) }}</p>
            </div>
            <div class="card p-5 border-t-4 border-emerald-500">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Collected</p>
                <p class="text-2xl font-bold text-emerald-600">৳{{ number_format($collected, 0) }}</p>
                @if($expected > 0)
                <div class="mt-2"><div class="h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, round($collected / $expected * 100)) }}%"></div></div></div>
                @endif
            </div>
            <div class="card p-5 border-t-4 border-red-400">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Still Due</p>
                <p class="text-2xl font-bold text-red-600">৳{{ number_format($due, 0) }}</p>
            </div>
        </div>
    </div>

    {{-- ── Fund Summary ───────────────────────────────────── --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Fund Summary <span class="text-gray-300 normal-case">· {{ $range->label }}</span></h2>
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-medium text-gray-400">Member Contribution</p><span class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center"><i class="fas fa-hand-holding-dollar text-blue-600 text-xs"></i></span></div>
                <p class="text-lg font-bold text-blue-700">৳{{ number_format($finance['total_member_contribution'], 0) }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Fees ৳{{ number_format($finance['monthly_collection'], 0) }} + Booster ৳{{ number_format($finance['booster_collection'], 0) }}</p>
            </div>
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-medium text-gray-400">Other Income</p><span class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center"><i class="fas fa-plus text-teal-600 text-xs"></i></span></div>
                <p class="text-lg font-bold text-teal-700">৳{{ number_format($finance['total_other_income'], 0) }}</p>
            </div>
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-medium text-gray-400">Expenses</p><span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center"><i class="fas fa-arrow-up text-red-600 text-xs"></i></span></div>
                <p class="text-lg font-bold text-red-600">৳{{ number_format($finance['total_expenses'], 0) }}</p>
            </div>
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-medium text-gray-400">Active FDR</p><span class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center"><i class="fas fa-building-columns text-violet-600 text-xs"></i></span></div>
                <p class="text-lg font-bold text-violet-700">৳{{ number_format($finance['total_active_fdr'], 0) }}</p>
            </div>
            <div class="card p-4 bg-gradient-to-br from-emerald-500 to-emerald-600 border-0 shadow-lg shadow-emerald-200">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-medium text-emerald-100">Net Fund</p><span class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center"><i class="fas fa-wallet text-white text-xs"></i></span></div>
                <p class="text-lg font-bold text-white">৳{{ number_format($netFund, 0) }}</p>
            </div>
        </div>
    </div>

    {{-- ── Bank & Cash Flow ───────────────────────────────── --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Bank &amp; Cash Flow <span class="text-gray-300 normal-case">· {{ $range->label }}</span></h2>
            <a href="{{ route('member.statements.club-finance') }}?{{ $range->queryString() }}" class="text-xs text-blue-600 hover:underline font-medium">Full statement <i class="fas fa-chevron-right text-[10px]"></i></a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-medium text-gray-400">Bank Deposits</p><span class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center"><i class="fas fa-money-bill-transfer text-emerald-600 text-xs"></i></span></div>
                <p class="text-lg font-bold text-emerald-700">৳{{ number_format($finance['total_bank_deposits'], 0) }}</p>
            </div>
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-medium text-gray-400">Cash in Hand</p><span class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center"><i class="fas fa-sack-dollar text-amber-600 text-xs"></i></span></div>
                <p class="text-lg font-bold {{ $finance['cash_in_hand'] < 0 ? 'text-red-600' : 'text-amber-600' }}">৳{{ number_format($finance['cash_in_hand'], 0) }}</p>
            </div>
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-medium text-gray-400">Bank Balance</p><span class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center"><i class="fas fa-wallet text-blue-600 text-xs"></i></span></div>
                <p class="text-lg font-bold text-blue-700">৳{{ number_format($finance['total_available_balance'], 0) }}</p>
            </div>
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-medium text-gray-400">FDR Interest</p><span class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center"><i class="fas fa-arrow-trend-up text-teal-600 text-xs"></i></span></div>
                <p class="text-lg font-bold text-teal-700">৳{{ number_format($finance['total_fdr_interest'], 0) }}</p>
            </div>
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3"><p class="text-xs font-medium text-gray-400">Withdrawn</p><span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center"><i class="fas fa-money-bill-wave text-red-600 text-xs"></i></span></div>
                <p class="text-lg font-bold text-red-600">৳{{ number_format($finance['total_withdrawals'], 0) }}</p>
            </div>
        </div>

        {{-- Bank-wise table (masked) --}}
        @if($bankRows->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto mt-4">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Bank</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Deposited</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Available</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Active FDR</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Interest</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Withdrawn</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($bankRows as $r)
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
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-2"><i class="fas fa-lock"></i> Read-only · account numbers masked · updates automatically as the admin records transactions.</p>
        @endif
    </div>

    {{-- ── Recent payments + FDR list ─────────────────────── --}}
    <div class="grid md:grid-cols-2 gap-6">
        <div class="card">
            <div class="card-header"><h2 class="text-sm font-semibold text-gray-900">Recent Approved Payments</h2><span class="text-xs text-gray-400">{{ $range->label }}</span></div>
            <div class="divide-y divide-gray-50">
                @forelse($recentTransactions as $t)
                <div class="flex items-center justify-between px-5 py-3.5">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $t->member->user->name ?? 'Member' }}</p>
                        <p class="text-xs text-gray-400">{{ date('F', mktime(0,0,0,$t->month,1)) }} {{ $t->year }} · {{ $t->payment_date->format('d M Y') }}</p>
                    </div>
                    <p class="text-sm font-bold text-emerald-600">৳{{ number_format($t->amount, 0) }}</p>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-sm text-gray-400"><i class="fas fa-inbox text-2xl text-gray-200 mb-2 block"></i>No payments in this period.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h2 class="text-sm font-semibold text-gray-900">FDR Summary</h2></div>
            <div class="divide-y divide-gray-50">
                @forelse($fdrSummary as $fdr)
                <div class="flex items-center justify-between px-5 py-3.5">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $fdr->bank_name }}</p>
                        <p class="text-xs text-gray-400">{{ $fdr->interest_rate }}% · Matures {{ $fdr->maturity_date->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">৳{{ number_format($fdr->principal_amount, 0) }}</p>
                        @php $c = ['active'=>'active','matured'=>'approved','renewed'=>'pending','closed'=>'voided'][$fdr->status] ?? 'closed'; @endphp
                        <span class="badge-{{ $c }}">{{ ucfirst($fdr->status) }}</span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-sm text-gray-400"><i class="fas fa-building-columns text-2xl text-gray-200 mb-2 block"></i>No FDR records.</div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
