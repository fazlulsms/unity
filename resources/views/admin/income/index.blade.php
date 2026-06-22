@extends('layouts.app')
@section('title', 'Income')
@section('page-title', 'Income Management')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-500">Total Active: <strong class="text-green-600">৳{{ number_format($totalActive, 2) }}</strong></div>
        <a href="{{ route('admin.income.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">+ Add Income</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Date</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Type</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Source</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Amount</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($incomes as $i)
                <tr class="hover:bg-gray-50 {{ $i->status === 'voided' ? 'opacity-60' : '' }}">
                    <td class="px-5 py-3 text-gray-600">{{ $i->date->format('d M Y') }}</td>
                    <td class="px-5 py-3"><span class="bg-teal-100 text-teal-700 px-2 py-0.5 rounded text-xs">{{ $i->income_type_label }}</span></td>
                    <td class="px-5 py-3 text-gray-700">{{ $i->source }}</td>
                    <td class="px-5 py-3 font-semibold text-green-600">৳{{ number_format($i->amount, 2) }}</td>
                    <td class="px-5 py-3"><span class="badge-{{ $i->status === 'active' ? 'active' : 'voided' }}">{{ ucfirst($i->status) }}</span></td>
                    <td class="px-5 py-3 flex gap-2">
                        <a href="{{ route('admin.income.show', $i) }}" class="text-gray-500 text-xs hover:underline">View</a>
                        <a href="{{ route('admin.income.edit', $i) }}" class="text-blue-600 text-xs hover:underline">Edit</a>
                        @if($i->isActive())
                        <form action="{{ route('admin.income.void', $i) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-red-500 text-xs hover:underline" onclick="return confirm('Void this income entry?')">Void</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No income records.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($incomes->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $incomes->links() }}</div>
        @endif
    </div>
</div>
@endsection
