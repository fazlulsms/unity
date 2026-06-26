@extends('layouts.app')
@section('title', 'Review Payment')
@section('page-title', 'Review Payment')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-2xl space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    <div class="card">
        <div class="card-body">
            <div class="flex justify-between items-start mb-5 pb-5 border-b border-gray-100">
                <div>
                    <h2 class="font-bold text-gray-900 text-lg">{{ $submission->member->user->name }}</h2>
                    <p class="text-sm text-gray-500 font-mono">{{ $submission->member->member_number }}</p>
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
                    <p class="text-xs text-gray-400 font-medium">{{ $label }}</p>
                    <p class="text-sm font-semibold text-gray-900 mt-0.5">{{ $value }}</p>
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
                <p class="text-xs text-gray-400 font-medium mb-2">Payment Proof</p>
                <a href="{{ $submission->proof_url }}" target="_blank"
                   class="inline-flex items-center gap-2 text-sm text-blue-600 hover:underline border border-blue-200 bg-blue-50 px-3 py-2 rounded-lg">
                    <i class="fas fa-paperclip"></i> View Proof Attachment
                </a>
            </div>
            @endif

            @if($submission->isPending())
            <div class="grid sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                <form action="{{ route('admin.payments.approve', $submission) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="form-label">Approval Remarks (optional)</label>
                        <textarea name="approval_remarks" rows="3" class="form-textarea text-sm"></textarea>
                    </div>
                    <button type="submit" class="btn-success w-full">
                        <i class="fas fa-check"></i> Approve & Generate Receipt
                    </button>
                </form>

                <form action="{{ route('admin.payments.reject', $submission) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="form-label">Rejection Reason <span class="form-required">*</span></label>
                        <textarea name="rejection_reason" rows="3" required class="form-textarea text-sm"></textarea>
                    </div>
                    <button type="submit" class="btn-danger w-full"
                        onclick="return confirm('Reject this payment?')">
                        <i class="fas fa-times"></i> Reject Payment
                    </button>
                </form>
            </div>

            @elseif($submission->isApproved())
            <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-100">
                <p class="text-sm font-semibold text-emerald-800">
                    <i class="fas fa-check-circle mr-1"></i>
                    Approved by {{ $submission->approver?->name }} on {{ $submission->approved_at->format('d M Y') }}
                </p>
                @if($submission->approval_remarks)
                <p class="text-xs text-emerald-600 mt-1">{{ $submission->approval_remarks }}</p>
                @endif
                @if($submission->receipt)
                <div class="mt-3 flex gap-2 flex-wrap">
                    <a href="{{ route('member.receipts.download', $submission->receipt) }}"
                       target="_blank" class="btn btn-sm btn-secondary">
                        <i class="fas fa-download"></i> Download Receipt
                    </a>
                    @if(!str_ends_with($submission->member->user->email ?? '', '@unity.local') && $submission->member->user->email)
                    <form method="POST" action="{{ route('admin.email.receipt.resend', $submission->receipt) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-secondary text-blue-600"
                                onclick="return confirm('Resend receipt email?')">
                            <i class="fas fa-envelope"></i> Email Receipt
                        </button>
                    </form>
                    @endif
                </div>
                @endif
            </div>

            @elseif($submission->status === 'rejected')
            <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                <p class="text-sm font-semibold text-red-700"><i class="fas fa-times-circle mr-1"></i> Payment Rejected</p>
                @if($submission->rejection_reason)
                <p class="text-sm text-red-600 mt-1">{{ $submission->rejection_reason }}</p>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Email history --}}
    @include('partials.email-log', ['emailLogs' => $emailLogs])

    <a href="{{ route('admin.payments.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to payments
    </a>
</div>
@endsection
