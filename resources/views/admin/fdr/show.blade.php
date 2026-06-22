@extends('layouts.app')
@section('title', 'FDR — ' . $fdr->fdr_number)
@section('page-title', 'FDR Record Details')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
@php
    $ext = $fdr->attachment ? strtolower(pathinfo($fdr->attachment, PATHINFO_EXTENSION)) : null;
    $statusBadge = ['active' => 'active', 'matured' => 'approved', 'renewed' => 'pending', 'closed' => 'voided'][$fdr->status] ?? 'voided';
@endphp
<div class="max-w-2xl space-y-5">

    {{-- Header card --}}
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-2xl font-bold text-gray-900">৳ {{ number_format($fdr->principal_amount, 2) }}</span>
                        <span class="badge-{{ $statusBadge }}">{{ ucfirst($fdr->status) }}</span>
                        @if($fdr->is_public_reference)
                        <span class="badge-info bg-blue-50 text-blue-700 ring-1 ring-blue-200 badge text-xs">Public</span>
                        @endif
                    </div>
                    <p class="text-gray-700 font-medium">{{ $fdr->bank_name }}{{ $fdr->branch ? ' — ' . $fdr->branch : '' }}</p>
                    <p class="text-xs text-gray-400 mt-1 font-mono">FDR #{{ $fdr->fdr_number }}</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('admin.fdr.edit', $fdr) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Key figures --}}
    <div class="grid sm:grid-cols-3 gap-4">
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Principal</p>
            <p class="text-xl font-bold text-gray-900">৳ {{ number_format($fdr->principal_amount, 2) }}</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Interest Rate</p>
            <p class="text-xl font-bold text-blue-600">{{ $fdr->interest_rate }}% p.a.</p>
        </div>
        <div class="stat-card text-center">
            <p class="text-xs text-gray-400 font-medium mb-1">Interest Received</p>
            <p class="text-xl font-bold text-emerald-600">৳ {{ number_format($fdr->interest_received, 2) }}</p>
        </div>
    </div>

    {{-- Dates & details --}}
    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm">FDR Details</p></div>
        <div class="card-body grid sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs text-gray-400 font-medium">Opening Date</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $fdr->opening_date->format('d F Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Maturity Date</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $fdr->maturity_date->format('d F Y') }}</p>
                @if($fdr->status === 'active')
                @php $days = $fdr->days_to_maturity; @endphp
                <p class="text-xs {{ $days < 0 ? 'text-red-500' : ($days <= 30 ? 'text-amber-500' : 'text-gray-400') }} mt-0.5">
                    {{ $days >= 0 ? $days . ' days remaining' : abs($days) . ' days overdue' }}
                </p>
                @endif
            </div>
            @if($fdr->expected_maturity_amount)
            <div>
                <p class="text-xs text-gray-400 font-medium">Expected Maturity Amount</p>
                <p class="text-sm text-gray-800 mt-0.5">৳ {{ number_format($fdr->expected_maturity_amount, 2) }}</p>
            </div>
            @endif
            <div>
                <p class="text-xs text-gray-400 font-medium">Added By</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $fdr->creator?->name ?? '—' }}</p>
            </div>
            @if($fdr->notes)
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-400 font-medium">Notes</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $fdr->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Attachment --}}
    @if($fdr->attachment_url)
    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm">FDR Document</p></div>
        <div class="card-body">
            @if(in_array($ext, ['jpg', 'jpeg', 'png']))
            <img src="{{ $fdr->attachment_url }}"
                 alt="FDR Document"
                 class="max-w-full rounded-lg border border-gray-200 mb-3"
                 style="max-height: 420px; object-fit: contain;">
            @endif
            <a href="{{ $fdr->attachment_url }}" target="_blank"
               class="btn btn-md btn-secondary">
                <i class="fas fa-{{ $ext === 'pdf' ? 'file-pdf text-red-500' : 'image text-blue-500' }}"></i>
                {{ $ext === 'pdf' ? 'Open PDF' : 'Open Image' }}
            </a>
        </div>
    </div>
    @endif

    <a href="{{ route('admin.fdr.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to FDR Records
    </a>
</div>
@endsection
