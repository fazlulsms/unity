@extends('layouts.app')
@section('title', 'My Dashboard')
@section('page-title', 'Dashboard')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="space-y-6 max-w-screen-lg">

    {{-- ── Welcome Banner ────────────────────────────────── --}}
    @if($member)
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl p-6 shadow-lg shadow-blue-200/50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-full opacity-10 pointer-events-none">
            <i class="fas fa-users text-white" style="font-size:10rem; position:absolute; right:-1rem; top:-1rem;"></i>
        </div>
        <div class="relative flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <img src="{{ auth()->user()->photo_url }}" alt="Photo"
                 class="w-16 h-16 rounded-2xl object-cover ring-4 ring-white/30 shadow-xl shrink-0">
            <div class="flex-1 min-w-0">
                <p class="text-blue-200 text-sm font-medium mb-0.5">Welcome back,</p>
                <h2 class="text-2xl font-extrabold text-white tracking-tight">{{ auth()->user()->name }}</h2>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="inline-flex items-center gap-1.5 text-xs bg-white/15 text-blue-100 px-2.5 py-1 rounded-lg font-medium">
                        <i class="fas fa-id-card text-xs"></i> {{ $member->member_number }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs bg-white/15 text-blue-100 px-2.5 py-1 rounded-lg font-medium">
                        <i class="fas fa-calendar text-xs"></i> Joined {{ $member->join_date->format('M Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs bg-emerald-400/20 text-emerald-200 px-2.5 py-1 rounded-lg font-medium border border-emerald-400/20">
                        <i class="fas fa-circle text-[8px]"></i> {{ ucfirst($member->status) }}
                    </span>
                </div>
            </div>
            <div class="shrink-0 text-right sm:pl-5 sm:border-l sm:border-white/20">
                <p class="text-blue-200 text-xs font-medium mb-1">Monthly Fee</p>
                <p class="text-3xl font-extrabold text-white">৳{{ number_format($member->monthly_fee_amount, 0) }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Quick Stats ────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Paid</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <i class="fas fa-check text-emerald-600 text-xs"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">৳{{ number_format($totalPaid, 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Lifetime approved</p>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount Due</p>
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-exclamation text-red-500 text-xs"></i>
                </div>
            </div>
            <p class="text-2xl font-bold {{ ($member?->total_due ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' }}">
                ৳{{ number_format($member?->total_due ?? 0, 0) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Outstanding balance</p>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Approved</p>
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-receipt text-blue-600 text-xs"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-blue-600">{{ $approvedPayments }}</p>
            <p class="text-xs text-gray-400 mt-1">Receipts available</p>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="fas fa-clock text-amber-500 text-xs"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $pendingPayments }}</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting approval</p>
        </div>
    </div>

    {{-- ── Recent Payments + Notices ──────────────────────── --}}
    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Recent Payments --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">Recent Payments</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Your latest fee submissions</p>
                </div>
                <a href="{{ route('member.fees.create') }}"
                   class="btn-primary btn-sm shrink-0">
                    <i class="fas fa-plus"></i> Submit
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentPayments as $p)
                <div class="flex items-center gap-4 px-5 py-3.5">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-money-bill-wave text-gray-400 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900">
                            {{ date('F', mktime(0,0,0,$p->month,1)) }} {{ $p->year }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $p->payment_date->format('d M Y') }} · {{ ucfirst($p->payment_method) }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-gray-900">৳{{ number_format($p->amount, 0) }}</p>
                        <span class="badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <i class="fas fa-receipt text-3xl text-gray-200 mb-3 block"></i>
                    <p class="text-sm text-gray-500 font-medium">No payments yet</p>
                    <p class="text-xs text-gray-400 mb-4">Submit your first monthly fee</p>
                    <a href="{{ route('member.fees.create') }}" class="btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Submit Payment
                    </a>
                </div>
                @endforelse
            </div>
            @if($recentPayments->isNotEmpty())
            <div class="px-5 py-3 border-t border-gray-50">
                <a href="{{ route('member.fees.index') }}" class="text-xs text-blue-600 font-medium hover:underline">
                    View all payments →
                </a>
            </div>
            @endif
        </div>

        {{-- Notices --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">Latest Notices</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Club announcements</p>
                </div>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($notices as $notice)
                <div class="flex gap-3 px-5 py-3.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fas fa-bell text-blue-400 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 leading-snug">{{ $notice->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $notice->published_at?->format('d M Y') }}</p>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <i class="fas fa-bell-slash text-3xl text-gray-200 mb-3 block"></i>
                    <p class="text-sm text-gray-400">No notices yet.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── Quick Actions ──────────────────────────────────── --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <a href="{{ route('member.fees.create') }}"
           class="card p-5 flex items-center gap-4 hover:border-blue-200 hover:shadow-md transition-all group">
            <div class="w-11 h-11 rounded-xl bg-blue-100 group-hover:bg-blue-600 flex items-center justify-center shrink-0 transition-colors">
                <i class="fas fa-plus text-blue-600 group-hover:text-white transition-colors"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Submit Payment</p>
                <p class="text-xs text-gray-400">Record your monthly fee</p>
            </div>
        </a>
        <a href="{{ route('member.fees.index') }}"
           class="card p-5 flex items-center gap-4 hover:border-emerald-200 hover:shadow-md transition-all group">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 group-hover:bg-emerald-600 flex items-center justify-center shrink-0 transition-colors">
                <i class="fas fa-clock-rotate-left text-emerald-600 group-hover:text-white transition-colors"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Payment History</p>
                <p class="text-xs text-gray-400">View & download receipts</p>
            </div>
        </a>
        <a href="{{ route('member.transparency') }}"
           class="card p-5 flex items-center gap-4 hover:border-violet-200 hover:shadow-md transition-all group">
            <div class="w-11 h-11 rounded-xl bg-violet-100 group-hover:bg-violet-600 flex items-center justify-center shrink-0 transition-colors">
                <i class="fas fa-chart-pie text-violet-600 group-hover:text-white transition-colors"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Club Finances</p>
                <p class="text-xs text-gray-400">View club transparency data</p>
            </div>
        </a>
    </div>

</div>
@endsection
