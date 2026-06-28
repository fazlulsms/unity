@extends('layouts.app')
@section('title', 'Bank Withdrawal')
@section('page-title', 'Bank Withdrawal Detail')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-lg space-y-5">
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Amount Withdrawn</p>
                <p class="text-3xl font-bold text-red-600">৳{{ number_format($bankWithdrawal->amount, 2) }}</p>
            </div>
            <a href="{{ route('admin.bank-withdrawals.edit', $bankWithdrawal) }}" class="text-xs border border-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50">Edit</a>
        </div>
        <dl class="space-y-2.5 text-sm border-t border-gray-100 pt-4">
            <div class="flex justify-between"><dt class="text-gray-500">Withdrawal Date</dt><dd class="font-medium text-gray-900">{{ $bankWithdrawal->withdrawal_date->format('d M Y') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Bank Account</dt><dd class="font-medium text-gray-900 text-right">{{ $bankWithdrawal->bankAccount->bank_name ?? '—' }}<br><span class="text-xs font-mono text-gray-400">{{ $bankWithdrawal->bankAccount->account_number ?? '' }}</span></dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Cheque Number</dt><dd class="font-medium text-gray-900 font-mono">{{ $bankWithdrawal->cheque_number ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Purpose</dt><dd class="font-medium text-gray-900">{{ $bankWithdrawal->purpose ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Recorded By</dt><dd class="font-medium text-gray-900">{{ $bankWithdrawal->creator->name ?? '—' }}</dd></div>
            @if($bankWithdrawal->remarks)
            <div class="pt-2"><dt class="text-gray-500 mb-1">Remarks</dt><dd class="text-gray-700">{{ $bankWithdrawal->remarks }}</dd></div>
            @endif
        </dl>
        @if($bankWithdrawal->attachment_url)
        <a href="{{ $bankWithdrawal->attachment_url }}" target="_blank" class="mt-4 inline-flex items-center gap-2 text-sm text-blue-600 hover:underline"><i class="fas fa-paperclip"></i> View attachment</a>
        @endif
    </div>
    <a href="{{ route('admin.bank-withdrawals.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Back to withdrawals</a>
</div>
@endsection
