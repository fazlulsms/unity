@extends('layouts.app')
@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('sidebar')
    <a href="{{ route('member.dashboard') }}" class="sidebar-link">
        <i class="fas fa-home w-4"></i> Dashboard
    </a>
    <a href="{{ route('member.fees.index') }}" class="sidebar-link active">
        <i class="fas fa-money-bill w-4"></i> My Payments
    </a>
@endsection

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="font-bold text-gray-900 text-lg">
                    {{ date('F', mktime(0,0,0,$submission->month,1)) }} {{ $submission->year }}
                </h2>
                <p class="text-sm text-gray-500">Submitted {{ $submission->created_at->format('d M Y h:i A') }}</p>
            </div>
            <span class="badge-{{ $submission->status }} text-sm">{{ ucfirst($submission->status) }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4 py-4 border-y border-gray-100">
            <div>
                <p class="text-xs text-gray-400">Amount</p>
                <p class="font-semibold text-gray-900">৳{{ number_format($submission->amount, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Payment Date</p>
                <p class="font-semibold text-gray-900">{{ $submission->payment_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Method</p>
                <p class="font-semibold text-gray-900 capitalize">{{ $submission->payment_method }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Reference</p>
                <p class="font-semibold text-gray-900">{{ $submission->transaction_reference ?: '—' }}</p>
            </div>
        </div>

        @if($submission->notes)
        <div>
            <p class="text-xs text-gray-400 mb-1">Notes</p>
            <p class="text-sm text-gray-600">{{ $submission->notes }}</p>
        </div>
        @endif

        @if($submission->isApproved())
        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
            <p class="text-sm font-semibold text-green-800 mb-1">Approved</p>
            <p class="text-xs text-green-700">By {{ $submission->approver?->name }} on {{ $submission->approved_at->format('d M Y') }}</p>
            @if($submission->approval_remarks)
            <p class="text-xs text-green-600 mt-1">{{ $submission->approval_remarks }}</p>
            @endif
        </div>
        @if($submission->receipt)
        <a href="{{ route('member.receipts.download', $submission->receipt) }}"
            class="w-full flex items-center justify-center gap-2 bg-green-600 text-white py-2.5 rounded-lg font-medium hover:bg-green-700 transition-colors text-sm">
            <i class="fas fa-download"></i> Download Receipt
        </a>
        @endif
        @endif

        @if($submission->status === 'rejected')
        <div class="bg-red-50 rounded-lg p-4 border border-red-100">
            <p class="text-sm font-semibold text-red-800 mb-1">Rejected</p>
            <p class="text-xs text-red-700">{{ $submission->rejection_reason }}</p>
        </div>
        @endif

        @if($submission->proof_attachment)
        <div>
            <p class="text-xs text-gray-400 mb-2">Payment Proof</p>
            <a href="{{ $submission->proof_url }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline">
                <i class="fas fa-paperclip"></i> View Attachment
            </a>
        </div>
        @endif

        <a href="{{ route('member.fees.index') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700">← Back to payments</a>
    </div>
</div>
@endsection
