@extends('layouts.app')
@section('title', 'Expenses')
@section('page-title', 'Expense Management')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-500">Total Active: <strong class="text-gray-900">৳{{ number_format($totalActive, 2) }}</strong></div>
        <a href="{{ route('admin.expenses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">+ Add Expense</a>
    </div>

    <form class="flex gap-3 flex-wrap">
        <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
        <input type="date" name="to" value="{{ request('to') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
        <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition-colors">Filter</button>
        <a href="{{ route('admin.expenses.index') }}" class="text-gray-500 px-3 py-2 text-sm">Clear</a>
    </form>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Date</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Category</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Description</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Amount</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Method</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($expenses as $e)
                <tr class="hover:bg-gray-50 {{ $e->status === 'voided' ? 'opacity-60' : '' }}">
                    <td class="px-5 py-3 text-gray-600">{{ $e->date->format('d M Y') }}</td>
                    <td class="px-5 py-3"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs">{{ $e->category }}</span></td>
                    <td class="px-5 py-3 text-gray-700 max-w-xs truncate">{{ $e->description }}</td>
                    <td class="px-5 py-3 font-semibold text-red-600">৳{{ number_format($e->amount, 2) }}</td>
                    <td class="px-5 py-3 text-gray-500 capitalize">{{ $e->payment_method }}</td>
                    <td class="px-5 py-3"><span class="badge-{{ $e->status === 'active' ? 'active' : 'voided' }}">{{ ucfirst($e->status) }}</span></td>
                    <td class="px-5 py-3 flex gap-2">
                        <a href="{{ route('admin.expenses.edit', $e) }}" class="text-blue-600 text-xs hover:underline">Edit</a>
                        @if($e->isActive())
                        <form action="{{ route('admin.expenses.void', $e) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-red-500 text-xs hover:underline" onclick="return confirm('Void this expense?')">Void</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No expenses recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($expenses->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $expenses->links() }}</div>
        @endif
    </div>
</div>
@endsection
