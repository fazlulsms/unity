@extends('layouts.app')
@section('title', 'Members')
@section('page-title', 'All Members')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-screen-xl space-y-5">

    <div class="filter-bar">
        <form class="flex flex-wrap items-center gap-3 flex-1" method="GET">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or phone…"
                   class="form-input w-auto flex-1 max-w-xs">
            <select name="status" class="form-select w-auto">
                <option value="">All Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended"{{ request('status') === 'suspended'? 'selected' : '' }}>Suspended</option>
            </select>
            <button type="submit" class="btn-primary btn-sm">Search</button>
            <a href="{{ route('admin.members.index') }}" class="btn-ghost btn-sm">Clear</a>
        </form>
        <p class="text-xs text-gray-400 shrink-0">{{ $members->total() }} members</p>
    </div>

    <div class="table-wrap">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Member</th>
                    <th class="th hidden sm:table-cell">Member #</th>
                    <th class="th hidden md:table-cell">Joined</th>
                    <th class="th hidden lg:table-cell">Monthly Fee</th>
                    <th class="th">Status</th>
                    <th class="th text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $m)
                <tr class="tr">
                    <td class="td">
                        <div class="flex items-center gap-3">
                            <img src="{{ $m->user->photo_url }}" alt=""
                                 class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100 shrink-0">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $m->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $m->user->phone }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="td hidden sm:table-cell">
                        <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                            {{ $m->member_number }}
                        </span>
                    </td>
                    <td class="td hidden md:table-cell text-gray-500">{{ $m->join_date->format('d M Y') }}</td>
                    <td class="td hidden lg:table-cell font-semibold text-gray-700">৳{{ number_format($m->monthly_fee_amount, 0) }}</td>
                    <td class="td">
                        <span class="badge-{{ $m->status }}">{{ ucfirst($m->status) }}</span>
                    </td>
                    <td class="td text-right">
                        <a href="{{ route('admin.members.show', $m) }}" class="btn-secondary btn-xs">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="table-empty">
                        <i class="fas fa-users-slash text-3xl text-gray-200 mb-2 block"></i>
                        No members found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($members->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $members->links() }}</div>
        @endif
    </div>
</div>
@endsection
