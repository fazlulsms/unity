@extends('layouts.app')
@section('title', 'Due Report')
@section('page-title', 'Due Report')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-4">
    <div class="flex justify-between">
        <p class="text-sm text-gray-500">{{ $members->count() }} members with dues</p>
        <a href="{{ route('admin.reports.dues') }}?export=pdf" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">PDF</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Member</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Member No.</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Monthly Fee</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Expected</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Paid</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Due</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($members as $m)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $m['user_name'] }}</td>
                    <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $m['member_number'] }}</td>
                    <td class="px-5 py-3">৳{{ number_format($m['monthly_fee_amount'], 2) }}</td>
                    <td class="px-5 py-3">৳{{ number_format($m['expected_total'], 2) }}</td>
                    <td class="px-5 py-3 text-green-600">৳{{ number_format($m['paid_total'], 2) }}</td>
                    <td class="px-5 py-3 font-semibold text-red-600">৳{{ number_format($m['due_amount'], 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No dues! All members are up to date.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
