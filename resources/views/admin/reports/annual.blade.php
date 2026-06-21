@extends('layouts.app')
@section('title', 'Annual Summary')
@section('page-title', 'Annual Fund Summary')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-6">
    <div class="flex gap-3 items-center">
        <form class="flex gap-3 items-center">
            <label class="text-sm text-gray-600">Year:</label>
            <select name="year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">View</button>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">PDF</a>
        </form>
    </div>

    <div class="grid sm:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Collections', 'value' => '৳' . number_format($totalCollections, 2), 'color' => 'blue'],
            ['label' => 'Total Expenses',    'value' => '৳' . number_format($totalExpenses, 2),    'color' => 'red'],
            ['label' => 'Other Income',      'value' => '৳' . number_format($totalIncome, 2),      'color' => 'teal'],
            ['label' => 'Net Balance',       'value' => '৳' . number_format($netBalance, 2),       'color' => 'green'],
        ] as $s)
        <div class="stat-card">
            <p class="text-xs text-gray-400 mb-1">{{ $s['label'] }}</p>
            <p class="text-xl font-bold text-{{ $s['color'] }}-600">{{ $s['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Monthly Breakdown — {{ $year }}</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Month</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Collections</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Expenses</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Income</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Net</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($monthlyBreakdown as $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $row['month_name'] }}</td>
                    <td class="px-5 py-3 text-green-600">৳{{ number_format($row['collected'], 2) }}</td>
                    <td class="px-5 py-3 text-red-600">৳{{ number_format($row['expenses'], 2) }}</td>
                    <td class="px-5 py-3 text-teal-600">৳{{ number_format($row['income'], 2) }}</td>
                    <td class="px-5 py-3 font-semibold {{ ($row['collected'] + $row['income'] - $row['expenses']) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ৳{{ number_format($row['collected'] + $row['income'] - $row['expenses'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 border-t border-gray-200">
                <tr>
                    <td class="px-5 py-3 font-bold text-gray-900">Total</td>
                    <td class="px-5 py-3 font-bold text-green-600">৳{{ number_format($totalCollections, 2) }}</td>
                    <td class="px-5 py-3 font-bold text-red-600">৳{{ number_format($totalExpenses, 2) }}</td>
                    <td class="px-5 py-3 font-bold text-teal-600">৳{{ number_format($totalIncome, 2) }}</td>
                    <td class="px-5 py-3 font-bold text-{{ $netBalance >= 0 ? 'green' : 'red' }}-600">৳{{ number_format($netBalance, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
