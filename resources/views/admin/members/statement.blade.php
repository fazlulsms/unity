@extends('layouts.app')
@section('title', 'Statement — ' . $member->member_number)
@section('page-title', 'Member Statement')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-4xl space-y-5">

    {{-- Header --}}
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <img src="{{ $member->user->photo_url }}"
                         class="w-14 h-14 rounded-xl object-cover border border-gray-200 shrink-0" alt="">
                    <div>
                        <p class="font-bold text-gray-900">{{ $member->user->name }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $member->member_number }} · Joined {{ $member->join_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Monthly fee: ৳ {{ number_format($member->monthly_fee_amount, 2) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    {{-- Year picker --}}
                    <form method="GET" class="flex items-center gap-2">
                        <select name="year" class="form-select w-auto text-sm" onchange="this.form.submit()">
                            @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('admin.members.statement', [$member, 'year' => $year, 'export' => 'pdf']) }}"
                       class="btn btn-sm btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Expected ({{ $year }})</p>
            <p class="text-2xl font-bold text-gray-700">৳ {{ number_format($totals['expected'], 2) }}</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Paid</p>
            <p class="text-2xl font-bold text-emerald-600">৳ {{ number_format($totals['paid'], 2) }}</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Outstanding</p>
            <p class="text-2xl font-bold {{ $totals['due'] > 0 ? 'text-red-600' : 'text-gray-400' }}">৳ {{ number_format($totals['due'], 2) }}</p>
        </div>
    </div>

    {{-- Monthly table --}}
    <div class="table-wrap">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Month</th>
                    <th class="th text-right">Expected</th>
                    <th class="th text-right">Paid</th>
                    <th class="th text-right hidden sm:table-cell">Due</th>
                    <th class="th hidden md:table-cell">Method</th>
                    <th class="th hidden md:table-cell">Payment Date</th>
                    <th class="th hidden lg:table-cell">Receipt No.</th>
                    <th class="th">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr class="tr">
                    <td class="td font-medium text-gray-800">{{ $row['month_name'] }} {{ $year }}</td>
                    <td class="td text-right text-gray-600">৳ {{ number_format($row['expected'], 2) }}</td>
                    <td class="td text-right font-medium {{ $row['paid'] > 0 ? 'text-emerald-600' : 'text-gray-400' }}">
                        {{ $row['paid'] > 0 ? '৳ ' . number_format($row['paid'], 2) : '—' }}
                    </td>
                    <td class="td text-right hidden sm:table-cell {{ $row['due'] > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                        {{ $row['due'] > 0 ? '৳ ' . number_format($row['due'], 2) : '—' }}
                    </td>
                    <td class="td hidden md:table-cell text-gray-500 text-xs">{{ $row['method'] }}</td>
                    <td class="td hidden md:table-cell text-gray-500 text-xs">{{ $row['payment_date'] }}</td>
                    <td class="td hidden lg:table-cell">
                        <span class="font-mono text-xs text-gray-500">{{ $row['receipt_number'] }}</span>
                    </td>
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
                <tr><td colspan="8" class="table-empty">No data for {{ $year }}.</td></tr>
                @endforelse
                @if($totals['joining_contribution'] > 0)
                <tr class="bg-amber-50/60 border-b border-amber-100">
                    <td class="td font-semibold text-amber-800">Opening / Joining Contribution</td>
                    <td class="td text-right font-semibold text-amber-700">৳ {{ number_format($totals['joining_contribution'], 2) }}</td>
                    <td class="td text-right text-gray-400 text-sm">—</td>
                    <td class="td text-right hidden sm:table-cell font-semibold text-amber-700">৳ {{ number_format($totals['joining_contribution'], 2) }}</td>
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
                    <td class="td font-bold text-gray-800">Total</td>
                    <td class="td text-right font-bold text-gray-700">৳ {{ number_format($totals['expected'], 2) }}</td>
                    <td class="td text-right font-bold text-emerald-600">৳ {{ number_format($totals['paid'], 2) }}</td>
                    <td class="td text-right font-bold hidden sm:table-cell {{ $totals['due'] > 0 ? 'text-red-600' : 'text-gray-400' }}">৳ {{ number_format($totals['due'], 2) }}</td>
                    <td colspan="4" class="td hidden md:table-cell"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <a href="{{ route('admin.members.show', $member) }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to member profile
    </a>
</div>
@endsection
