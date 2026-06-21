@extends('layouts.app')
@section('title', 'Review Payment')
@section('page-title', 'Review Payment')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-2xl space-y-5">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex justify-between items-start mb-5 pb-5 border-b border-gray-100">
            <div>
                <h2 class="font-bold text-gray-900 text-lg">{{ $submission->member->user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $submission->member->member_number }}</p>
            </div>
            <span class="badge-{{ $submission->status }} text-sm">{{ ucfirst($submission->status) }}</span>
        </div>

        <div class="grid grid-cols-2 gap-5 mb-5">
            @foreach([
                ['Period', date('F', mktime(0,0,0,$submission->month,1)) . ' ' . $submission->year],
                ['Amount', '৳' . number_format($submission->amount, 2)],
                ['Payment Date', $submission->payment_date->format('d M Y')],
                ['Method', ucfirst($submission->payment_method)],
                ['Reference', $submission->transaction_reference ?: '—'],
                ['Submitted', $submission->created_at->format('d M Y')],
            ] as [$label, $value])
            <div>
                <p class="text-xs text-gray-400">{{ $label }}</p>
                <p class="text-sm font-semibold text-gray-900">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        @if($submission->notes)
        <div class="bg-gray-50 rounded-lg p-3 mb-5 text-sm text-gray-600">
            <strong class="text-gray-700">Member Notes:</strong> {{ $submission->notes }}
        </div>
        @endif

        @if($submission->proof_attachment)
        <div class="mb-5">
            <p class="text-xs text-gray-400 mb-2">Payment Proof</p>
            <a href="{{ $submission->proof_url }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline border border-blue-200 bg-blue-50 px-3 py-2 rounded-lg">
                <i class="fas fa-paperclip"></i> View Proof Attachment
            </a>
        </div>
        @endif

        @if($submission->isPending())
        <div class="grid sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
            <form action="{{ route('admin.payments.approve', $submission) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Approval Remarks (optional)</label>
                    <textarea name="approval_remarks" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg font-medium hover:bg-green-700 transition-colors text-sm">
                    Approve & Generate Receipt
                </button>
            </form>

            <form action="{{ route('admin.payments.reject', $submission) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Rejection Reason <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" rows="3" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-red-600 text-white py-2.5 rounded-lg font-medium hover:bg-red-700 transition-colors text-sm"
                    onclick="return confirm('Reject this payment?')">
                    Reject Payment
                </button>
            </form>
        </div>
        @elseif($submission->isApproved())
        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
            <p class="text-sm font-semibold text-green-800">Approved by {{ $submission->approver?->name }} on {{ $submission->approved_at->format('d M Y') }}</p>
            @if($submission->approval_remarks)
            <p class="text-xs text-green-600 mt-1">{{ $submission->approval_remarks }}</p>
            @endif
        </div>
        @endif
    </div>

    <a href="{{ route('admin.payments.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">← Back to payments</a>
</div>
@endsection
