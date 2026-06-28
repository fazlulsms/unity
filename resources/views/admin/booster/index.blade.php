@extends('layouts.app')
@section('title', 'Booster Contributions')
@section('page-title', 'Booster Contributions')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5 max-w-screen-xl">

    @include('partials.period-filter', ['range' => $range, 'action' => route('admin.booster.index')])

    <div class="grid grid-cols-2 xl:grid-cols-3 gap-4">
        <div class="card p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Expected</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">৳{{ number_format($totals['expected'], 0) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Collected</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">৳{{ number_format($totals['deposited'], 0) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Due</p>
            <p class="text-2xl font-bold text-red-600 mt-1">৳{{ number_format($totals['due'], 0) }}</p>
        </div>
    </div>

    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ $rows->count() }} drive(s) · {{ $range->label }}</p>
        <a href="{{ route('admin.booster.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">+ New Booster Drive</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Title</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Period</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Per Member</th>
                    <th class="text-center px-5 py-3 text-xs font-medium text-gray-500">Members</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Expected</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Collected</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Due</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($rows as $r)
                @php $d = $r['drive']; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $d->title }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $d->period_date->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-right text-gray-700">৳{{ number_format($d->expected_amount_per_member, 0) }}</td>
                    <td class="px-5 py-3 text-center text-gray-600">{{ $d->members_count }}</td>
                    <td class="px-5 py-3 text-right text-gray-700">৳{{ number_format($r['expected'], 0) }}</td>
                    <td class="px-5 py-3 text-right text-emerald-600 font-medium">৳{{ number_format($r['deposited'], 0) }}</td>
                    <td class="px-5 py-3 text-right text-red-600">৳{{ number_format($r['due'], 0) }}</td>
                    <td class="px-5 py-3"><span class="badge-{{ $d->isActive() ? 'active' : 'closed' }}">{{ ucfirst($d->status) }}</span></td>
                    <td class="px-5 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.booster.show', $d) }}" class="text-gray-500 text-xs hover:underline">View</a>
                            <a href="{{ route('admin.booster.edit', $d) }}" class="text-blue-600 text-xs hover:underline">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-5 py-10 text-center text-gray-400">No Booster Contribution drives in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
