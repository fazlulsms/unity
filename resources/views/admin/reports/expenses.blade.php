@extends('layouts.app')
@section('title', 'Expense Report')
@section('page-title', 'Expense Report')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-4">
    <form class="flex gap-3 items-center">
        <label class="text-sm text-gray-600">Year:</label>
        <select name="year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            @for($y = now()->year; $y >= now()->year - 3; $y--)
            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">Filter</button>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">PDF</a>
    </form>

    <div class="grid sm:grid-cols-3 gap-4">
        <div class="stat-card">
            <p class="text-xs text-gray-400 mb-1">Total Expenses</p>
            <p class="text-xl font-bold text-red-600">৳{{ number_format($total, 2) }}</p>
        </div>
        @foreach($byCategory->take(2) as $cat => $amount)
        <div class="stat-card">
            <p class="text-xs text-gray-400 mb-1">{{ $cat }}</p>
            <p class="text-xl font-bold text-gray-900">৳{{ number_format($amount, 2) }}</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Date</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Category</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Description</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Amount</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Method</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($expenses as $e)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-600">{{ $e->date->format('d M Y') }}</td>
                    <td class="px-5 py-3"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs">{{ $e->category }}</span></td>
                    <td class="px-5 py-3 text-gray-700 max-w-xs truncate">{{ $e->description }}</td>
                    <td class="px-5 py-3 font-semibold text-red-600">৳{{ number_format($e->amount, 2) }}</td>
                    <td class="px-5 py-3 text-gray-500 capitalize">{{ $e->payment_method }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No expenses for {{ $year }}.</td></tr>
                @endforelse
            </tbody>
            @if($expenses->isNotEmpty())
            <tfoot class="bg-gray-50 border-t border-gray-200">
                <tr>
                    <td colspan="3" class="px-5 py-3 font-bold text-gray-900">Total</td>
                    <td class="px-5 py-3 font-bold text-red-600">৳{{ number_format($total, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
