@extends('layouts.app')
@section('title', 'Club Finances')
@section('page-title', 'Club Transparency Dashboard')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="max-w-screen-lg space-y-6">

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-5">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-users text-blue-600 text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['activeMembers'] }}</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Active Members</p>
        </div>
        <div class="card p-5">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-wallet text-emerald-600 text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600">৳{{ number_format($stats['fundBalance'], 0) }}</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Fund Balance</p>
        </div>
        <div class="card p-5">
            <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-building-columns text-violet-600 text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-violet-600">৳{{ number_format($stats['totalFdr'], 0) }}</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">FDR Investment</p>
        </div>
        <div class="card p-5">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-chart-pie text-amber-600 text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['collectionPercent'] }}%</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">This Month Collected</p>
        </div>
        <div class="card p-5">
            <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-arrow-trend-up text-teal-600 text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-teal-600">৳{{ number_format($stats['totalIncome'], 0) }}</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Total Income</p>
        </div>
        <div class="card p-5">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-arrow-trend-down text-red-500 text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-red-600">৳{{ number_format($stats['totalExpense'], 0) }}</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Total Expenses</p>
        </div>
        <div class="card p-5">
            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-calendar-check text-indigo-600 text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-indigo-600">৳{{ number_format($stats['expected'], 0) }}</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Expected This Month</p>
        </div>
        <div class="card p-5">
            <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fas fa-users text-gray-500 text-sm"></i>
            </div>
            <p class="text-2xl font-bold text-gray-700">{{ $stats['totalMembers'] }}</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Total Members</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Recent approved payments --}}
        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-900">Recent Approved Payments</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentTransactions as $t)
                <div class="flex items-center justify-between px-5 py-3.5">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $t->member->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ date('F', mktime(0,0,0,$t->month,1)) }} {{ $t->year }}</p>
                    </div>
                    <p class="text-sm font-bold text-emerald-600">৳{{ number_format($t->amount, 0) }}</p>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-sm text-gray-400">
                    <i class="fas fa-inbox text-2xl text-gray-200 mb-2 block"></i>
                    No transactions yet.
                </div>
                @endforelse
            </div>
        </div>

        {{-- FDR summary --}}
        <div class="card">
            <div class="card-header">
                <h2 class="text-sm font-semibold text-gray-900">FDR Summary</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($fdrSummary as $fdr)
                <div class="flex items-center justify-between px-5 py-3.5">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $fdr->bank_name }}</p>
                        <p class="text-xs text-gray-400">{{ $fdr->interest_rate }}% · Matures {{ $fdr->maturity_date->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">৳{{ number_format($fdr->principal_amount, 0) }}</p>
                        <span class="badge-{{ $fdr->status }}">{{ ucfirst($fdr->status) }}</span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center text-sm text-gray-400">
                    <i class="fas fa-building-columns text-2xl text-gray-200 mb-2 block"></i>
                    No FDR records.
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
