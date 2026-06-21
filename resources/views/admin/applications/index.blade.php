@extends('layouts.app')
@section('title', 'Applications')
@section('page-title', 'Membership Applications')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-screen-xl space-y-5">

    <div class="filter-bar">
        <form class="flex flex-wrap items-center gap-3 flex-1" method="GET">
            <select name="status" class="form-select w-auto">
                <option value="">All Status</option>
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit" class="btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.applications.index') }}" class="btn-ghost btn-sm">Clear</a>
        </form>
        <p class="text-xs text-gray-400 shrink-0">{{ $applications->total() }} total</p>
    </div>

    <div class="table-wrap">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Applicant</th>
                    <th class="th">Phone</th>
                    <th class="th hidden sm:table-cell">Monthly Fee</th>
                    <th class="th hidden md:table-cell">Applied</th>
                    <th class="th">Status</th>
                    <th class="th text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr class="tr">
                    <td class="td">
                        <p class="font-semibold text-gray-900">{{ $app->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $app->email }}</p>
                    </td>
                    <td class="td text-gray-600">{{ $app->phone }}</td>
                    <td class="td hidden sm:table-cell">৳{{ number_format($app->monthly_fee_amount, 0) }}</td>
                    <td class="td hidden md:table-cell text-gray-500">{{ $app->created_at->format('d M Y') }}</td>
                    <td class="td">
                        <span class="badge-{{ $app->status }}">
                            @if($app->status === 'pending') <i class="fas fa-clock"></i>
                            @elseif($app->status === 'approved') <i class="fas fa-check"></i>
                            @else <i class="fas fa-times"></i> @endif
                            {{ ucfirst($app->status) }}
                        </span>
                    </td>
                    <td class="td text-right">
                        <a href="{{ route('admin.applications.show', $app) }}" class="btn-primary btn-xs">
                            Review <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="table-empty">
                        <i class="fas fa-inbox text-3xl text-gray-200 mb-2 block"></i>
                        No applications found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($applications->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
@endsection
