@extends('layouts.public')
@section('title', 'Unity Club — Home')

@section('content')

{{-- ── Hero ─────────────────────────────────────────── --}}
<section class="relative bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 overflow-hidden">
    {{-- Decorative blobs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-600/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -left-20 w-60 h-60 bg-blue-500/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-24 md:py-32 text-center">
        <span class="inline-flex items-center gap-2 bg-blue-500/10 border border-blue-500/20 text-blue-300
                     text-xs font-semibold px-4 py-1.5 rounded-full mb-6">
            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse"></span>
            Private Friendship Club
        </span>
        <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white mb-6 leading-tight tracking-tight">
            Where Friends Build<br class="hidden sm:block">
            <span class="text-blue-400">Financial Futures</span>
        </h1>
        <p class="text-slate-300 text-lg md:text-xl mb-10 max-w-2xl mx-auto leading-relaxed">
            Unity Club is a private circle of trusted friends contributing monthly, investing together,
            and growing collectively — with full financial transparency.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('apply') }}"
               class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-7 py-3.5 rounded-xl
                      hover:bg-blue-500 transition-all shadow-xl shadow-blue-900/50 text-base">
                <i class="fas fa-paper-plane"></i> Apply for Membership
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/20 text-white
                      font-semibold px-7 py-3.5 rounded-xl hover:bg-white/20 transition-all text-base">
                <i class="fas fa-right-to-bracket"></i> Member Login
            </a>
        </div>
    </div>
</section>

{{-- ── Public Stat Cards ──────────────────────────── --}}
<section class="bg-white border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-y md:divide-y-0 divide-gray-100">
            <div class="py-8 px-6 text-center">
                <p class="text-3xl font-extrabold text-blue-600">{{ $stats['activeMembers'] }}</p>
                <p class="text-sm text-gray-500 font-medium mt-1">Active Members</p>
            </div>
            <div class="py-8 px-6 text-center">
                <p class="text-3xl font-extrabold text-emerald-600">৳{{ number_format($stats['fundBalance'], 0) }}</p>
                <p class="text-sm text-gray-500 font-medium mt-1">Total Fund Balance</p>
            </div>
            <div class="py-8 px-6 text-center">
                <p class="text-3xl font-extrabold text-violet-600">৳{{ number_format($stats['totalFdr'], 0) }}</p>
                <p class="text-sm text-gray-500 font-medium mt-1">FDR Investment</p>
            </div>
            <div class="py-8 px-6 text-center">
                <p class="text-3xl font-extrabold text-amber-500">{{ $stats['collectionPercent'] }}%</p>
                <p class="text-sm text-gray-500 font-medium mt-1">This Month Collected</p>
            </div>
        </div>
    </div>
</section>

{{-- ── Why Join ─────────────────────────────────────── --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Why Join Unity Club?</h2>
            <p class="text-gray-500 text-lg max-w-xl mx-auto">
                A trusted network of friends building wealth together, one month at a time.
            </p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-shield-halved text-blue-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Transparent Finances</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Every transaction is recorded and visible to all members through our public dashboard.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-piggy-bank text-emerald-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Collective Savings</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Monthly contributions build a strong fund that benefits every member of the club.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-building-columns text-violet-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">FDR Investments</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Club funds are securely invested in bank Fixed Deposit Receipts for guaranteed returns.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-people-group text-amber-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Strong Community</h3>
                <p class="text-sm text-gray-500 leading-relaxed">A trusted circle of friends supporting each other with shared goals and values.</p>
            </div>
        </div>
    </div>
</section>

{{-- ── Notices + CTA ──────────────────────────────── --}}
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid lg:grid-cols-3 gap-12">

            {{-- Notices column --}}
            <div class="lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold text-gray-900">Latest Notices</h2>
                    <a href="{{ route('notices') }}" class="text-blue-600 text-sm font-semibold hover:underline">
                        View all →
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($notices as $notice)
                    <div class="flex gap-4 p-4 rounded-xl border border-gray-100 hover:border-blue-100 hover:bg-blue-50/30 transition-colors">
                        <div class="w-10 h-10 rounded-lg
                            @if($notice->type === 'notice') bg-blue-100
                            @elseif($notice->type === 'announcement') bg-emerald-100
                            @else bg-amber-100 @endif
                            flex items-center justify-center shrink-0">
                            <i class="fas
                                @if($notice->type === 'notice') fa-bell text-blue-600
                                @elseif($notice->type === 'announcement') fa-bullhorn text-emerald-600
                                @else fa-file-lines text-amber-600 @endif
                                text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                    @if($notice->type === 'notice') bg-blue-100 text-blue-700
                                    @elseif($notice->type === 'announcement') bg-emerald-100 text-emerald-700
                                    @else bg-amber-100 text-amber-700 @endif">
                                    {{ ucfirst($notice->type) }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $notice->published_at?->format('d M Y') }}</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 text-sm">{{ $notice->title }}</h4>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 text-gray-400">
                        <i class="fas fa-bell-slash text-3xl mb-3 block"></i>
                        <p class="text-sm">No notices yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- CTA + Join column --}}
            <div class="space-y-6">
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-7 text-white shadow-xl shadow-blue-200">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-5">
                        <i class="fas fa-door-open text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Ready to Join?</h3>
                    <p class="text-blue-100 text-sm leading-relaxed mb-6">
                        Fill out our simple application. Our team reviews it within a few days and you'll get a welcome email with your account.
                    </p>
                    <a href="{{ route('apply') }}"
                       class="inline-flex items-center gap-2 bg-white text-blue-700 font-bold px-5 py-2.5 rounded-xl text-sm
                              hover:bg-blue-50 transition-colors shadow-lg">
                        <i class="fas fa-paper-plane"></i> Apply Now
                    </a>
                </div>

                <div class="card p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Already a Member?</h3>
                    <p class="text-sm text-gray-500 mb-4">Access your personal portal to submit payments, track dues, and download receipts.</p>
                    <a href="{{ route('login') }}" class="btn-primary w-full justify-center">
                        <i class="fas fa-right-to-bracket"></i> Login to Portal
                    </a>
                    <a href="{{ route('transparency') }}" class="btn-secondary w-full justify-center mt-2">
                        <i class="fas fa-chart-pie"></i> View Transparency
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
