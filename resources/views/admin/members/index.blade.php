@extends('layouts.app')
@section('title', 'Member Database')
@section('page-title', 'Member Database')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    {{-- Filters --}}
    <form method="GET" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name, phone, email…" class="form-input max-w-xs">
        <select name="status" class="form-select w-auto">
            <option value="">All Statuses</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>Inactive</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
        <button type="submit" class="btn-primary btn-sm">Filter</button>
        @if(request('search') || request('status'))
        <a href="{{ route('admin.members.index') }}" class="btn-ghost btn-sm">Clear</a>
        @endif
        <span class="ml-auto text-xs text-gray-400">{{ $members->total() }} members</span>
    </form>

    <div class="table-wrap">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Member</th>
                    <th class="th hidden sm:table-cell">Member ID</th>
                    <th class="th hidden md:table-cell">Email</th>
                    <th class="th hidden lg:table-cell">Joined</th>
                    <th class="th hidden xl:table-cell">Monthly Fee</th>
                    <th class="th">Status</th>
                    <th class="th text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr class="tr">
                    <td class="td">
                        <div class="flex items-center gap-3">
                            @if($member->user->photo)
                                <img src="{{ asset('storage/' . $member->user->photo) }}"
                                     class="w-9 h-9 rounded-full object-cover border border-gray-200 shrink-0" alt="">
                            @else
                                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                    <span class="text-blue-600 font-bold text-xs">{{ substr($member->user->name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $member->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $member->user->phone ?? '—' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="td hidden sm:table-cell">
                        <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $member->member_number }}</span>
                    </td>
                    <td class="td hidden md:table-cell text-gray-500 text-xs">{{ $member->user->email ?? '—' }}</td>
                    <td class="td hidden lg:table-cell text-gray-500 text-xs">{{ $member->join_date->format('d M Y') }}</td>
                    <td class="td hidden xl:table-cell text-gray-700 text-sm">৳ {{ number_format($member->monthly_fee_amount, 0) }}</td>
                    <td class="td"><span class="badge-{{ $member->status }}">{{ ucfirst($member->status) }}</span></td>
                    <td class="td text-right">
                        <a href="{{ route('admin.members.show', $member) }}" class="btn btn-sm btn-primary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="table-empty">No members found.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($members->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $members->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
