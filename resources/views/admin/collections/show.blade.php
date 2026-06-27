@extends('layouts.app')
@section('title', 'Payment #' . $collection->id)
@section('page-title', 'Payment Details')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
@php $ext = $collection->proof_attachment ? strtolower(pathinfo($collection->proof_attachment, PATHINFO_EXTENSION)) : null; @endphp
<div class="max-w-2xl space-y-4">

    {{-- ── Header card ──────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body">

            {{-- Top row: member + amount + actions --}}
            <div class="flex items-start justify-between gap-4">

                {{-- Member info --}}
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ $collection->member->user->photo_url }}"
                         class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0" alt="">
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 text-sm leading-tight truncate">
                            {{ $collection->member->user->name }}
                        </p>
                        <p class="text-xs font-mono text-gray-400 mt-0.5">
                            {{ $collection->member->member_number }}
                        </p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="text-xl font-bold text-emerald-600">
                                ৳ {{ number_format($collection->amount, 2) }}
                            </span>
                            <span class="badge-approved">Approved</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ date('F', mktime(0,0,0,$collection->month,1)) }} {{ $collection->year }}
                        </p>
                    </div>
                </div>

                {{-- Action buttons (self-start prevents stretching) --}}
                <div class="flex items-center gap-2 shrink-0 self-start flex-wrap justify-end">
                    @if($collection->receipt)
                    <a href="{{ route('member.receipts.download', $collection->receipt) }}"
                       target="_blank"
                       class="btn btn-sm btn-success">
                        <i class="fas fa-download"></i> Receipt
                    </a>
                    @if(!str_ends_with($collection->member->user->email ?? '', '@unity.local') && $collection->member->user->email)
                    <form method="POST" action="{{ route('admin.email.receipt.resend', $collection->receipt) }}">
                        @csrf
                        <button type="submit"
                                class="btn btn-sm btn-secondary"
                                onclick="return confirm('{{ $receiptEmailSent ? 'Resend' : 'Send' }} receipt email to {{ addslashes($collection->member->user->name) }}?')">
                            <i class="fas fa-envelope"></i>
                            {{ $receiptEmailSent ? 'Resend Email' : 'Send Email' }}
                        </button>
                    </form>
                    @endif
                    @endif
                    <a href="{{ route('admin.members.show', $collection->member) }}"
                       class="btn btn-sm btn-secondary">
                        <i class="fas fa-user"></i> Member
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Payment details ──────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm">Payment Details</p>
        </div>
        <div class="card-body grid sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs text-gray-400 font-medium">Period</p>
                <p class="text-sm text-gray-800 mt-0.5">
                    {{ date('F', mktime(0,0,0,$collection->month,1)) }} {{ $collection->year }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Payment Date</p>
                <p class="text-sm text-gray-800 mt-0.5">
                    {{ $collection->payment_date->format('d F Y') }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Payment Method</p>
                <p class="text-sm text-gray-800 mt-0.5 capitalize">{{ $collection->payment_method }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Transaction Reference</p>
                <p class="text-sm text-gray-800 mt-0.5 font-mono">
                    {{ $collection->transaction_reference ?: '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Approved By</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $collection->approver?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Approved At</p>
                <p class="text-sm text-gray-800 mt-0.5">
                    {{ $collection->approved_at?->format('d M Y, h:i A') ?? '—' }}
                </p>
            </div>
            @if($collection->approval_remarks)
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-400 font-medium">Remarks</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $collection->approval_remarks }}</p>
            </div>
            @endif
            @if($collection->notes)
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-400 font-medium">Notes</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $collection->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Receipt card ──────────────────────────────────────────── --}}
    @if($collection->receipt)
    <div class="card">
        <div class="card-body flex items-center justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs text-gray-400 font-medium">Receipt Number</p>
                <p class="text-sm font-bold font-mono text-gray-900 mt-0.5">
                    {{ $collection->receipt->receipt_number }}
                </p>
                @if($receiptEmailSent)
                <p class="text-xs text-emerald-600 mt-1">
                    <i class="fas fa-check-circle"></i> Receipt email sent
                </p>
                @else
                <p class="text-xs text-amber-500 mt-1">
                    <i class="fas fa-clock"></i> Receipt email not yet sent
                </p>
                @endif
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('member.receipts.download', $collection->receipt) }}"
                   target="_blank"
                   class="btn btn-md btn-secondary">
                    <i class="fas fa-file-alt"></i> Download Receipt
                </a>
                @if(!str_ends_with($collection->member->user->email ?? '', '@unity.local') && $collection->member->user->email)
                <form method="POST" action="{{ route('admin.email.receipt.resend', $collection->receipt) }}">
                    @csrf
                    <button type="submit"
                            class="btn btn-md btn-secondary text-blue-600"
                            onclick="return confirm('{{ $receiptEmailSent ? 'Resend' : 'Send' }} receipt email?')">
                        <i class="fas fa-envelope"></i>
                        {{ $receiptEmailSent ? 'Resend Receipt Email' : 'Send Receipt Email' }}
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ── Proof attachment ──────────────────────────────────────── --}}
    @if($collection->proof_url)
    <div class="card">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm">Payment Proof</p>
        </div>
        <div class="card-body">
            @if(in_array($ext, ['jpg', 'jpeg', 'png']))
            <img src="{{ $collection->proof_url }}"
                 alt="Payment Proof"
                 class="max-w-full rounded-lg border border-gray-200 mb-3"
                 style="max-height: 420px; object-fit: contain;">
            @endif
            <a href="{{ $collection->proof_url }}" target="_blank" class="btn btn-md btn-secondary">
                <i class="fas fa-{{ $ext === 'pdf' ? 'file-pdf text-red-500' : 'image text-blue-500' }}"></i>
                {{ $ext === 'pdf' ? 'Open PDF' : 'Open Image' }}
            </a>
        </div>
    </div>
    @endif

    {{-- ── Email history ─────────────────────────────────────────── --}}
    @include('partials.email-log', ['emailLogs' => $emailLogs])

    <a href="{{ route('admin.collections.index') }}"
       class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to Collections
    </a>

</div>
@endsection
