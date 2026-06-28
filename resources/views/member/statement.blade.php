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
            <div class="flex items-center gap-2 shrink-0">
                <form method="GET" class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 font-medium">Year:</label>
                    <select name="year" class="form-select w-auto" onchange="this.form.submit()">
                        @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('member.statements.personal-pdf', ['year' => $year]) }}" class="btn-primary btn-sm whitespace-nowrap"><i class="fas fa-download"></i> PDF</a>
            </div>
        </div>
    </div>

    {{-- Summary — total member contribution (monthly + booster) --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Total Expected</p>
            <p class="text-2xl font-bold text-gray-700">৳ {{ number_format($totals['expected'], 0) }}</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Total Paid</p>
            <p class="text-2xl font-bold text-emerald-600">৳ {{ number_format($totals['paid'], 0) }}</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Outstanding</p>
            <p class="text-2xl font-bold {{ $totals['due'] > 0 ? 'text-red-600' : 'text-gray-400' }}">৳ {{ number_format($totals['due'], 0) }}</p>
        </div>
    </div>
    <p class="text-xs text-gray-400 -mt-2">Includes monthly fees ({{ $year }}) and Booster Contribution.</p>

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
                @if($totals['joining_contribution'] > 0)
                <tr class="bg-amber-50/60 border-b border-amber-100">
                    <td class="td font-semibold text-amber-800">Opening / Joining Contribution</td>
                    <td class="td text-right font-semibold text-amber-700">৳ {{ number_format($totals['joining_contribution'], 0) }}</td>
                    <td class="td text-right text-gray-400 text-sm">—</td>
                    <td class="td text-right hidden sm:table-cell font-semibold text-amber-700">৳ {{ number_format($totals['joining_contribution'], 0) }}</td>
                    <td class="td hidden md:table-cell text-gray-400 text-xs">—</td>
                    <td class="td hidden md:table-cell text-gray-400 text-xs">—</td>
                    <td class="td hidden lg:table-cell text-gray-400 text-xs">—</td>
                    <td class="td"><span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700">Opening</span></td>
                </tr>
                @endif
            </tbody>
            @if(count($rows) > 0)
            <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                <tr>
                    <td class="td font-bold">Monthly Total</td>
                    <td class="td text-right font-bold text-gray-700">৳ {{ number_format($totals['monthly_expected'], 0) }}</td>
                    <td class="td text-right font-bold text-emerald-600">৳ {{ number_format($totals['monthly_paid'], 0) }}</td>
                    <td class="td text-right font-bold hidden sm:table-cell {{ $totals['monthly_due'] > 0 ? 'text-red-600' : 'text-gray-400' }}">৳ {{ number_format($totals['monthly_due'], 0) }}</td>
                    <td colspan="4" class="hidden md:table-cell"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    {{-- Booster Contribution --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Booster Contribution</h2>
                <p class="text-xs text-gray-400 mt-0.5">Special contributions outside the monthly fee — counted as member contribution</p>
            </div>
        </div>
        <div class="card-body">
            <div class="grid sm:grid-cols-3 gap-4 mb-4">
                <div class="rounded-lg bg-gray-50 p-3 text-center">
                    <p class="text-xs text-gray-400 font-medium">Expected</p>
                    <p class="text-xl font-bold text-gray-700">৳ {{ number_format($totals['booster_expected'], 0) }}</p>
                </div>
                <div class="rounded-lg bg-emerald-50 p-3 text-center">
                    <p class="text-xs text-gray-400 font-medium">Paid</p>
                    <p class="text-xl font-bold text-emerald-600">৳ {{ number_format($totals['booster_paid'], 0) }}</p>
                </div>
                <div class="rounded-lg {{ $totals['booster_due'] > 0 ? 'bg-red-50' : 'bg-gray-50' }} p-3 text-center">
                    <p class="text-xs text-gray-400 font-medium">Due</p>
                    <p class="text-xl font-bold {{ $totals['booster_due'] > 0 ? 'text-red-600' : 'text-gray-400' }}">৳ {{ number_format($totals['booster_due'], 0) }}</p>
                </div>
            </div>
            @if(count($boosterRows) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="th">Drive</th>
                            <th class="th">Date</th>
                            <th class="th">Method</th>
                            <th class="th text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($boosterRows as $b)
                        <tr class="tr">
                            <td class="td font-medium text-gray-800">{{ $b['title'] }}</td>
                            <td class="td text-gray-500">{{ $b['date'] }}</td>
                            <td class="td text-gray-500">{{ $b['method'] }}</td>
                            <td class="td text-right font-medium text-emerald-600">৳ {{ number_format($b['amount'], 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-2">No booster payments recorded.</p>
            @endif
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('member.fees.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Payment history</a>
        <a href="{{ route('member.statements.index') }}" class="text-sm text-blue-600 hover:underline ml-auto">All statements & downloads →</a>
    </div>
</div>
@endsection
