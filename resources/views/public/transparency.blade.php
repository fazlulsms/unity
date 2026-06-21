@extends('layouts.public')
@section('title', 'Transparency Dashboard — Unity Club')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Public Transparency Dashboard</h1>
        <p class="text-gray-500 mt-1 text-sm">Real-time financial summary of Unity Club — updated automatically.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-10">
        @foreach([
            ['label' => 'Total Members',       'value' => $stats['totalMembers'],                        'icon' => 'users',       'color' => 'blue'],
            ['label' => 'Active Members',       'value' => $stats['activeMembers'],                       'icon' => 'user-check',  'color' => 'green'],
            ['label' => 'Fund Balance (৳)',     'value' => number_format($stats['fundBalance'], 2),       'icon' => 'wallet',      'color' => 'emerald'],
            ['label' => 'FDR Amount (৳)',       'value' => number_format($stats['totalFdr'], 2),          'icon' => 'university',  'color' => 'purple'],
            ['label' => 'Total Income (৳)',     'value' => number_format($stats['totalIncome'], 2),       'icon' => 'arrow-down',  'color' => 'teal'],
            ['label' => 'Total Expense (৳)',    'value' => number_format($stats['totalExpense'], 2),      'icon' => 'arrow-up',    'color' => 'red'],
            ['label' => 'Total Collection (৳)', 'value' => number_format($stats['totalCollection'], 2),  'icon' => 'money-bill',  'color' => 'blue'],
            ['label' => 'This Month %',         'value' => $stats['collectionPercent'] . '%',             'icon' => 'chart-bar',   'color' => 'orange'],
        ] as $s)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-{{ $s['color'] }}-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-{{ $s['icon'] }} text-{{ $s['color'] }}-600 text-sm"></i>
                </div>
                <p class="text-xs text-gray-500 font-medium">{{ $s['label'] }}</p>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid md:grid-cols-2 gap-8">
        {{-- Recent Transactions --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Recent Approved Payments</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentTransactions as $txn)
                <div class="px-5 py-3 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $txn->member->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ date('F', mktime(0,0,0,$txn->month,1)) }} {{ $txn->year }}</p>
                    </div>
                    <span class="text-sm font-semibold text-green-600">৳{{ number_format($txn->amount, 2) }}</span>
                </div>
                @empty
                <div class="px-5 py-6 text-center text-gray-400 text-sm">No transactions yet.</div>
                @endforelse
            </div>
        </div>

        {{-- FDR Summary --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">FDR Summary</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($fdrSummary as $fdr)
                    <div class="px-5 py-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $fdr->bank_name }}</p>
                                <p class="text-xs text-gray-400">{{ $fdr->interest_rate }}% p.a. · Matures {{ $fdr->maturity_date->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">৳{{ number_format($fdr->principal_amount, 0) }}</p>
                                <span class="badge-{{ $fdr->status === 'active' ? 'active' : 'approved' }}">{{ ucfirst($fdr->status) }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-6 text-center text-gray-400 text-sm">No FDR records yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Notices --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Recent Notices</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($notices as $notice)
                    <div class="px-5 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $notice->title }}</p>
                        <p class="text-xs text-gray-400">{{ $notice->published_at?->format('d M Y') }}</p>
                    </div>
                    @empty
                    <div class="px-5 py-6 text-center text-gray-400 text-sm">No notices.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
