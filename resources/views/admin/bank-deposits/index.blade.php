@extends('layouts.app')
@section('title', 'Bank Deposits')
@section('page-title', 'Bank Deposits')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5 max-w-screen-xl">

    <div class="flex flex-wrap justify-between items-center gap-3">
        <div class="text-sm text-gray-500">
            Total Deposited: <strong class="text-emerald-600">৳{{ number_format($totalDeposited, 2) }}</strong>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <select name="bank_account_id" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm cursor-pointer focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">All accounts</option>
                    @foreach($accounts as $a)
                    <option value="{{ $a->id }}" {{ request('bank_account_id') == $a->id ? 'selected' : '' }}>{{ $a->bank_name }} — {{ $a->account_number }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.bank-deposits.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors whitespace-nowrap">+ Add Deposit</a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Date</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Bank Account</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Source / Reference</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Amount</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Slip</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($deposits as $d)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-500">{{ $d->deposit_date->format('d M Y') }}</td>
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-900">{{ $d->bankAccount->bank_name ?? '—' }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $d->bankAccount->account_number ?? '' }}</p>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $d->source_reference ?: '—' }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-emerald-600">৳{{ number_format($d->amount, 2) }}</td>
                    <td class="px-5 py-3">
                        @if($d->attachment_url)
                        <a href="{{ $d->attachment_url }}" target="_blank" class="text-blue-600 text-xs hover:underline"><i class="fas fa-paperclip"></i></a>
                        @else <span class="text-gray-300">—</span> @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.bank-deposits.show', $d) }}" class="text-gray-500 text-xs hover:underline">View</a>
                            <a href="{{ route('admin.bank-deposits.edit', $d) }}" class="text-blue-600 text-xs hover:underline">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No deposits recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($deposits->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $deposits->links() }}</div>
        @endif
    </div>
</div>
@endsection
