@extends('layouts.app')
@section('title', 'Collection Payment #' . $collection->id)
@section('page-title', 'Payment Details')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
@php $ext = $collection->proof_attachment ? strtolower(pathinfo($collection->proof_attachment, PATHINFO_EXTENSION)) : null; @endphp
<div class="max-w-2xl space-y-5">

    {{-- Header --}}
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                {{-- Member info --}}
                <div class="flex items-center gap-4">
                    <img src="{{ $collection->member->user->photo_url }}"
                         class="w-14 h-14 rounded-xl object-cover border border-gray-200 shrink-0" alt="">
                    <div>
                        <p class="font-bold text-gray-900">{{ $collection->member->user->name }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $collection->member->member_number }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-2xl font-bold text-emerald-600">৳ {{ number_format($collection->amount, 2) }}</span>
                            <span class="badge-approved">Approved</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ date('F', mktime(0,0,0,$collection->month,1)) }} {{ $collection->year }}
                        </p>
                    </div>
                </div>
                {{-- Actions --}}
                <div class="flex gap-2 shrink-0">
                    @if($collection->receipt)
                    <a href="{{ route('member.receipts.download', $collection->receipt) }}"
                       target="_blank" class="btn btn-sm btn-success">
                        <i class="fas fa-download"></i> Receipt
                    </a>
                    @endif
                    <a href="{{ route('admin.members.show', $collection->member) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-user"></i> Member
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment details --}}
    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm">Payment Details</p></div>
        <div class="card-body grid sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs text-gray-400 font-medium">Period</p>
                <p class="text-sm text-gray-800 mt-0.5">
                    {{ date('F', mktime(0,0,0,$collection->month,1)) }} {{ $collection->year }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Payment Date</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $collection->payment_date->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Payment Method</p>
                <p class="text-sm text-gray-800 mt-0.5 capitalize">{{ $collection->payment_method }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Transaction Reference</p>
                <p class="text-sm text-gray-800 mt-0.5 font-mono">{{ $collection->transaction_reference ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Approved By</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $collection->approver?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Approved At</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $collection->approved_at?->format('d M Y, h:i A') ?? '—' }}</p>
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

    {{-- Receipt card --}}
    @if($collection->receipt)
    <div class="card">
        <div class="card-body flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-medium">Receipt Number</p>
                <p class="text-sm font-bold font-mono text-gray-900 mt-0.5">{{ $collection->receipt->receipt_number }}</p>
            </div>
            <a href="{{ route('member.receipts.download', $collection->receipt) }}"
               target="_blank" class="btn btn-md btn-secondary">
                <i class="fas fa-file-alt"></i> Download Receipt
            </a>
        </div>
    </div>
    @endif

    {{-- Proof attachment --}}
    @if($collection->proof_url)
    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm">Payment Proof</p></div>
        <div class="card-body">
            @if(in_array($ext, ['jpg', 'jpeg', 'png']))
            <img src="{{ $collection->proof_url }}"
                 alt="Payment Proof"
                 class="max-w-full rounded-lg border border-gray-200 mb-3"
                 style="max-height: 420px; object-fit: contain;">
            @endif
            <a href="{{ $collection->proof_url }}" target="_blank"
               class="btn btn-md btn-secondary">
                <i class="fas fa-{{ $ext === 'pdf' ? 'file-pdf text-red-500' : 'image text-blue-500' }}"></i>
                {{ $ext === 'pdf' ? 'Open PDF' : 'Open Image' }}
            </a>
        </div>
    </div>
    @endif

    <a href="{{ route('admin.collections.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to Collections
    </a>
</div>
@endsection
