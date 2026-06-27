@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-6 max-w-screen-xl">

    {{-- ── Top Member Stats ──────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Total Members --}}
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                <i class="fas fa-users text-blue-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalMembers }}</p>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Total Members</p>
            </div>
        </div>
        {{-- Active Members --}}
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                <i class="fas fa-user-check text-emerald-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $activeMembers }}</p>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Active Members</p>
            </div>
        </div>
        {{-- Pending Applications --}}
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                <i class="fas fa-file-circle-question text-amber-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $pendingApplications }}</p>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Pending Applications</p>
            </div>
        </div>
        {{-- Pending Payments --}}
        <div class="card p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center shrink-0">
                <i class="fas fa-clock text-violet-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $pendingPayments }}</p>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Pending Payments</p>
            </div>
        </div>
    </div>

    {{-- ── This Month ─────────────────────────────────────── --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
            {{ now()->format('F Y') }} Overview
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="card p-5 border-t-4 border-blue-500">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Expected</p>
                <p class="text-2xl font-bold text-gray-900">৳{{ number_format($expectedCollection, 0) }}</p>
                <p class="text-xs text-gray-400 mt-1">From {{ $activeMembers }} active members</p>
            </div>
            <div class="card p-5 border-t-4 border-emerald-500">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Collected</p>
                <p class="text-2xl font-bold text-emerald-600">৳{{ number_format($collectedThisMonth, 0) }}</p>
                @if($expectedCollection > 0)
                <div class="mt-2">
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full"
                             style="width: {{ min(100, round($collectedThisMonth / $expectedCollection * 100)) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ round($collectedThisMonth / $expectedCollection * 100) }}% collected</p>
                </div>
                @endif
            </div>
            <div class="card p-5 border-t-4 border-red-400">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Still Due</p>
                <p class="text-2xl font-bold text-red-600">৳{{ number_format($dueThisMonth, 0) }}</p>
                <p class="text-xs text-gray-400 mt-1">Outstanding this month</p>
            </div>
        </div>
    </div>

    {{-- ── Fund Summary ───────────────────────────────────── --}}
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Fund Summary (All Time)</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-medium text-gray-400">Collections</p>
                    <span class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-arrow-down text-blue-600 text-xs"></i>
                    </span>
                </div>
                <p class="text-lg font-bold text-blue-700">৳{{ number_format($totalCollection, 0) }}</p>
            </div>
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-medium text-gray-400">Expenses</p>
                    <span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center">
                        <i class="fas fa-arrow-up text-red-600 text-xs"></i>
                    </span>
                </div>
                <p class="text-lg font-bold text-red-600">৳{{ number_format($totalExpenses, 0) }}</p>
            </div>
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-medium text-gray-400">Other Income</p>
                    <span class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center">
                        <i class="fas fa-plus text-teal-600 text-xs"></i>
                    </span>
                </div>
                <p class="text-lg font-bold text-teal-700">৳{{ number_format($totalIncome, 0) }}</p>
            </div>
            <div class="card p-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-medium text-gray-400">FDR Principal</p>
                    <span class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                        <i class="fas fa-building-columns text-violet-600 text-xs"></i>
                    </span>
                </div>
                <p class="text-lg font-bold text-violet-700">৳{{ number_format($totalFdrPrincipal, 0) }}</p>
            </div>
            <div class="card p-4 bg-gradient-to-br from-emerald-500 to-emerald-600 border-0 shadow-lg shadow-emerald-200">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-medium text-emerald-100">Net Fund</p>
                    <span class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center">
                        <i class="fas fa-wallet text-white text-xs"></i>
                    </span>
                </div>
                <p class="text-lg font-bold text-white">৳{{ number_format($netFund, 0) }}</p>
            </div>
        </div>
    </div>

    {{-- ── Activity Tables ────────────────────────────────── --}}
    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Pending Payments --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">Pending Payments</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Awaiting your approval</p>
                </div>
                <a href="{{ route('admin.payments.index') }}" class="btn-ghost btn-sm text-blue-600">
                    View all <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($pendingPaymentsList as $p)
                <div class="flex items-center gap-3 px-5 py-3.5">
                    <img src="{{ $p->member->user->photo_url }}" class="w-8 h-8 rounded-full object-cover shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $p->member->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ date('F', mktime(0,0,0,$p->month,1)) }} {{ $p->year }}
                            · {{ ucfirst($p->payment_method) }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-gray-900">৳{{ number_format($p->amount, 0) }}</p>
                        <a href="{{ route('admin.payments.show', $p) }}"
                           class="text-xs text-blue-600 hover:text-blue-700 font-medium">Review →</a>
                    </div>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-circle-check text-emerald-500 text-lg"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500">All caught up!</p>
                    <p class="text-xs text-gray-400">No pending payments.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Pending Applications --}}
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">Pending Applications</h2>
                    <p class="text-xs text-gray-400 mt-0.5">New membership requests</p>
                </div>
                <a href="{{ route('admin.applications.index') }}" class="btn-ghost btn-sm text-blue-600">
                    View all <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentApplications as $app)
                <div class="flex items-center gap-3 px-5 py-3.5">
                    <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                        <span class="text-amber-700 font-bold text-xs">{{ substr($app->full_name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $app->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $app->phone }} · {{ $app->created_at->format('d M Y') }}</p>
                    </div>
                    <a href="{{ route('admin.applications.show', $app) }}"
                       class="badge-pending shrink-0">Review</a>
                </div>
                @empty
                <div class="px-5 py-10 text-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-gray-300 text-lg"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500">No new applications</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── Upcoming Events ───────────────────────────────── --}}
    @if($memberBirthdays->isNotEmpty() || $familyBirthdays->isNotEmpty() || $upcomingAnniversaries->isNotEmpty())
    <div>
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Upcoming Events (Next 30 Days)</h2>
        <div class="grid lg:grid-cols-3 gap-5">

            {{-- Member Birthdays --}}
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-birthday-cake text-orange-500 text-xs"></i>
                        </span>
                        <p class="font-semibold text-gray-800 text-sm">Member Birthdays</p>
                    </div>
                </div>
                @if($memberBirthdays->isNotEmpty())
                <div class="divide-y divide-gray-50">
                    @foreach($memberBirthdays as $m)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <img src="{{ $m->user->photo_url }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $m->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $m->user->date_of_birth->format('d M') }}</p>
                        </div>
                        <span class="text-xs font-semibold {{ $m->_days === 0 ? 'text-orange-600' : 'text-gray-500' }} shrink-0">
                            {{ $m->_days === 0 ? 'Today!' : 'in ' . $m->_days . 'd' }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-5 py-6 text-center text-sm text-gray-400">No upcoming birthdays.</div>
                @endif
            </div>

            {{-- Family Birthdays --}}
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-pink-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-heart text-pink-500 text-xs"></i>
                        </span>
                        <p class="font-semibold text-gray-800 text-sm">Family Birthdays</p>
                    </div>
                </div>
                @if($familyBirthdays->isNotEmpty())
                <div class="divide-y divide-gray-50">
                    @foreach($familyBirthdays as $f)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="w-8 h-8 rounded-full bg-pink-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-pink-400 text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $f->name }}</p>
                            <p class="text-xs text-gray-400">{{ $f->relationship_label }} of {{ $f->member->user->name }}</p>
                        </div>
                        <span class="text-xs font-semibold {{ $f->_days === 0 ? 'text-pink-600' : 'text-gray-500' }} shrink-0">
                            {{ $f->_days === 0 ? 'Today!' : 'in ' . $f->_days . 'd' }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-5 py-6 text-center text-sm text-gray-400">No upcoming family birthdays.</div>
                @endif
            </div>

            {{-- Marriage Anniversaries --}}
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-rose-100 flex items-center justify-center shrink-0">
                            <i class="fas fa-ring text-rose-500 text-xs"></i>
                        </span>
                        <p class="font-semibold text-gray-800 text-sm">Marriage Anniversaries</p>
                    </div>
                </div>
                @if($upcomingAnniversaries->isNotEmpty())
                <div class="divide-y divide-gray-50">
                    @foreach($upcomingAnniversaries as $a)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <img src="{{ $a->member->user->photo_url }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $a->member->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $a->marriage_anniversary->format('d M') }}</p>
                        </div>
                        <span class="text-xs font-semibold {{ $a->_days === 0 ? 'text-rose-600' : 'text-gray-500' }} shrink-0">
                            {{ $a->_days === 0 ? 'Today!' : 'in ' . $a->_days . 'd' }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-5 py-6 text-center text-sm text-gray-400">No upcoming anniversaries.</div>
                @endif
            </div>

        </div>
    </div>
    @endif

</div>
@endsection
