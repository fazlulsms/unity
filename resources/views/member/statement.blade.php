@extends('layouts.app')
@section('title', 'My Contribution Statement')
@section('page-title', 'Contribution Statement')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="max-w-4xl space-y-5">

    {{-- Header --}}
    <div class="card">
        <div class="card-body flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <p class="font-bold text-gray-900 text-lg">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $member->member_number }} · Joined {{ $member->join_date->format('d M Y') }}</p>
                <p class="text-sm text-gray-500 mt-1">Monthly contribution: <span class="font-semibold text-gray-700">৳ {{ number_format($member->monthly_fee_amount, 2) }}</span></p>
            </div>
            <form method="GET" class="flex items-center gap-2 shrink-0">
                <label class="text-sm text-gray-600 font-medium">Year:</label>
                <select name="year" class="form-select w-auto" onchange="this.form.submit()">
                    @foreach($availableYears as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- Summary --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Expected ({{ $year }})</p>
            <p class="text-2xl font-bold text-gray-700">৳ {{ number_format($totals['expected'], 0) }}</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Paid</p>
            <p class="text-2xl font-bold text-emerald-600">৳ {{ number_format($totals['paid'], 0) }}</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Outstanding</p>
            <p class="text-2xl font-bold {{ $totals['due'] > 0 ? 'text-red-600' : 'text-gray-400' }}">৳ {{ number_format($totals['due'], 0) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Month</th>
                    <th class="th text-right">Expected</th>
                    <th class="th text-right">Paid</th>
                    <th class="th text-right hidden sm:table-cell">Due</th>
                    <th class="th hidden md:table-cell">Method</th>
                    <th class="th hidden md:table-cell">Date</th>
                    <th class="th hidden lg:table-cell">Receipt</th>
                    <th class="th">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr class="tr">
                    <td class="td font-medium text-gray-800">{{ $row['month_name'] }}</td>
                    <td class="td text-right text-gray-600">৳ {{ number_format($row['expected'], 0) }}</td>
                    <td class="td text-right font-medium {{ $row['paid'] > 0 ? 'text-emerald-600' : 'text-gray-400' }}">
                        {{ $row['paid'] > 0 ? '৳ ' . number_format($row['paid'], 0) : '—' }}
                    </td>
                    <td class="td text-right hidden sm:table-cell {{ $row['due'] > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                        {{ $row['due'] > 0 ? '৳ ' . number_format($row['due'], 0) : '—' }}
                    </td>
                    <td class="td hidden md:table-cell text-gray-500 text-xs">{{ $row['method'] }}</td>
                    <td class="td hidden md:table-cell text-gray-500 text-xs">{{ $row['payment_date'] }}</td>
                    <td class="td hidden lg:table-cell font-mono text-xs text-gray-500">{{ $row['receipt_number'] }}</td>
                    <td class="td">
                        @if($row['status'] === 'paid')
                            <span class="badge-approved">Paid</span>
                        @elseif($row['status'] === 'partial')
                            <span class="badge-pending">Partial</span>
                        @else
                            <span class="badge-rejected">Due</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="table-empty">No contribution records for {{ $year }}.</td></tr>
                @endforelse
            </tbody>
            @if(count($rows) > 0)
            <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                <tr>
                    <td class="td font-bold">Total</td>
                    <td class="td text-right font-bold text-gray-700">৳ {{ number_format($totals['expected'], 0) }}</td>
                    <td class="td text-right font-bold text-emerald-600">৳ {{ number_format($totals['paid'], 0) }}</td>
                    <td class="td text-right font-bold hidden sm:table-cell {{ $totals['due'] > 0 ? 'text-red-600' : 'text-gray-400' }}">৳ {{ number_format($totals['due'], 0) }}</td>
                    <td colspan="4" class="hidden md:table-cell"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('member.fees.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Payment history</a>
    </div>
</div>
@endsection
