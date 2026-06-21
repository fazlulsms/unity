@extends('layouts.app')
@section('title', 'Payment Approvals')
@section('page-title', 'Payment Approvals')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-screen-xl space-y-5">

    <div class="filter-bar">
        <form class="flex flex-wrap items-center gap-3 flex-1" method="GET">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search member…"
                   class="form-input w-auto flex-1 max-w-xs">
            <select name="status" class="form-select w-auto">
                <option value="">Pending (default)</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="all"      {{ request('status') === 'all'      ? 'selected' : '' }}>All</option>
            </select>
            <button type="submit" class="btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.payments.index') }}" class="btn-ghost btn-sm">Clear</a>
        </form>
        <p class="text-xs text-gray-400 shrink-0">{{ $submissions->total() }} records</p>
    </div>

    <div class="table-wrap">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Member</th>
                    <th class="th hidden sm:table-cell">Period</th>
                    <th class="th">Amount</th>
                    <th class="th hidden md:table-cell">Method</th>
                    <th class="th hidden lg:table-cell">Submitted</th>
                    <th class="th">Status</th>
                    <th class="th text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $s)
                <tr class="tr">
                    <td class="td">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $s->member->user->photo_url }}"
                                 class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-100 shrink-0 hidden sm:block">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $s->member->user->name }}</p>
                                <p class="text-xs text-gray-400 sm:hidden">{{ date('F', mktime(0,0,0,$s->month,1)) }} {{ $s->year }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="td hidden sm:table-cell text-gray-600">{{ date('F', mktime(0,0,0,$s->month,1)) }} {{ $s->year }}</td>
                    <td class="td font-bold text-gray-900">৳{{ number_format($s->amount, 0) }}</td>
                    <td class="td hidden md:table-cell">
                        <span class="capitalize text-gray-500">{{ $s->payment_method }}</span>
                    </td>
                    <td class="td hidden lg:table-cell text-gray-500">{{ $s->created_at->format('d M Y') }}</td>
                    <td class="td">
                        <span class="badge-{{ $s->status }}">{{ ucfirst($s->status) }}</span>
                    </td>
                    <td class="td text-right">
                        <a href="{{ route('admin.payments.show', $s) }}"
                           class="{{ $s->status === 'pending' ? 'btn-warning' : 'btn-secondary' }} btn-xs">
                            {{ $s->status === 'pending' ? 'Review' : 'View' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="table-empty">
                        <i class="fas fa-circle-check text-3xl text-gray-200 mb-2 block"></i>
                        No payment submissions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($submissions->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $submissions->links() }}</div>
        @endif
    </div>
</div>
@endsection
