@extends('layouts.app')
@section('title', 'Collections Report')
@section('page-title', 'Collections Report')
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
        <label class="text-sm text-gray-600">Month:</label>
        <select name="month" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">All Months</option>
            @for($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
            @endfor
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">Filter</button>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">PDF</a>
    </form>

    <div class="flex justify-between items-center text-sm">
        <span class="text-gray-500">{{ $collections->count() }} records</span>
        <span class="font-bold text-gray-900">Total: ৳{{ number_format($total, 2) }}</span>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Member</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Period</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Amount</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Method</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Approved</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($collections as $c)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $c->member->user->name }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ date('F', mktime(0,0,0,$c->month,1)) }} {{ $c->year }}</td>
                    <td class="px-5 py-3 font-semibold text-green-600">৳{{ number_format($c->amount, 2) }}</td>
                    <td class="px-5 py-3 text-gray-500 capitalize">{{ $c->payment_method }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $c->approved_at?->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No collections found.</td></tr>
                @endforelse
            </tbody>
            @if($collections->isNotEmpty())
            <tfoot class="bg-gray-50 border-t border-gray-200">
                <tr>
                    <td colspan="2" class="px-5 py-3 font-bold text-gray-900">Total</td>
                    <td class="px-5 py-3 font-bold text-green-600">৳{{ number_format($total, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
