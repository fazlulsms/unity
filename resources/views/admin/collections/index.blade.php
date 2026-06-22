@extends('layouts.app')
@section('title', 'Collections')
@section('page-title', 'Collections')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    {{-- Summary cards --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="stat-card">
            <p class="text-xs text-gray-400 font-medium mb-1">This Month</p>
            <p class="text-2xl font-bold text-blue-600">৳ {{ number_format($summary['this_month'], 0) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $summary['count_this_month'] }} payment(s) — {{ now()->format('F Y') }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-400 font-medium mb-1">This Year</p>
            <p class="text-2xl font-bold text-emerald-600">৳ {{ number_format($summary['this_year'], 0) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ now()->year }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs text-gray-400 font-medium mb-1">All-Time Total</p>
            <p class="text-2xl font-bold text-gray-800">৳ {{ number_format($summary['total'], 0) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">All approved payments</p>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.collections.create') }}" class="btn btn-md btn-success">
            <i class="fas fa-plus"></i> Add Manual Payment
        </a>
        <a href="{{ route('admin.collections.bulk') }}" class="btn btn-md btn-secondary">
            <i class="fas fa-table-list"></i> Bulk Monthly Entry
        </a>
        <a href="{{ route('admin.collections.due') }}" class="btn btn-md btn-secondary">
            <i class="fas fa-clock-rotate-left"></i> Due List
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Search member</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Name or phone…" class="form-input w-52">
        </div>
        <div>
            <label class="form-label">Month</label>
            <select name="month" class="form-select">
                <option value="">All months</option>
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                    {{ date('F', mktime(0,0,0,$m,1)) }}
                </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="form-label">Year</label>
            <select name="year" class="form-select">
                <option value="">All years</option>
                @for($y = now()->year; $y >= 2020; $y--)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-md btn-secondary">
            <i class="fas fa-filter"></i> Filter
        </button>
        @if(request()->hasAny(['search','month','year']))
        <a href="{{ route('admin.collections.index') }}" class="btn btn-md btn-ghost text-gray-400">Clear</a>
        @endif
    </form>

    {{-- Collection history table --}}
    <div class="table-wrap">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm">
                Collection History
                @if(request()->hasAny(['search','month','year']))
                    <span class="text-gray-400 font-normal">(filtered)</span>
                @endif
            </p>
            <span class="text-xs text-gray-400">{{ $collections->total() }} record(s)</span>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Member</th>
                    <th class="th hidden sm:table-cell">Period</th>
                    <th class="th">Amount</th>
                    <th class="th hidden md:table-cell">Method</th>
                    <th class="th hidden lg:table-cell">Payment Date</th>
                    <th class="th hidden lg:table-cell">Reference</th>
                    <th class="th text-right">Receipt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collections as $col)
                <tr class="tr">
                    <td class="td">
                        <div class="flex items-center gap-2">
                            <img src="{{ $col->member->user->photo_url }}"
                                 class="w-7 h-7 rounded-full object-cover border border-gray-200 shrink-0" alt="">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $col->member->user->name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $col->member->member_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="td hidden sm:table-cell text-gray-700">
                        {{ date('F', mktime(0,0,0,$col->month,1)) }} {{ $col->year }}
                    </td>
                    <td class="td font-semibold text-gray-900">৳ {{ number_format($col->amount, 2) }}</td>
                    <td class="td hidden md:table-cell text-gray-500 capitalize">{{ $col->payment_method }}</td>
                    <td class="td hidden lg:table-cell text-gray-500 text-xs">{{ $col->payment_date->format('d M Y') }}</td>
                    <td class="td hidden lg:table-cell text-gray-400 text-xs font-mono">
                        {{ $col->transaction_reference ?: '—' }}
                    </td>
                    <td class="td text-right">
                        @if($col->receipt)
                        <a href="{{ route('member.receipts.download', $col->receipt) }}"
                           class="btn btn-sm btn-ghost text-xs" target="_blank">
                            <i class="fas fa-download"></i>
                        </a>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="table-empty">No collection records found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($collections->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $collections->links() }}</div>
        @endif
    </div>

</div>
@endsection
