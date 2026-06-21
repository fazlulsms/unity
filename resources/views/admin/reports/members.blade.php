@extends('layouts.app')
@section('title', 'Member Report')
@section('page-title', 'Member Report')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ $members->count() }} active members</p>
        <a href="{{ route('admin.reports.members') }}?export=pdf" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
            <i class="fas fa-file-pdf mr-1"></i> Export PDF
        </a>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">#</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Member</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Member No.</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Phone</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Joined</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Monthly Fee</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Total Paid</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Due</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($members as $i => $m)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $m->user->name }}</td>
                    <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $m->member_number }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $m->user->phone }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $m->join_date->format('d M Y') }}</td>
                    <td class="px-5 py-3">৳{{ number_format($m->monthly_fee_amount, 2) }}</td>
                    <td class="px-5 py-3 font-semibold text-green-600">৳{{ number_format($m->total_paid, 2) }}</td>
                    <td class="px-5 py-3 font-semibold text-red-600">৳{{ number_format($m->total_due, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
