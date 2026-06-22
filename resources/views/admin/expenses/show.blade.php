@extends('layouts.app')
@section('title', 'Expense #' . $expense->id)
@section('page-title', 'Expense Details')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
@php $ext = $expense->attachment ? strtolower(pathinfo($expense->attachment, PATHINFO_EXTENSION)) : null; @endphp
<div class="max-w-2xl space-y-5">

    {{-- Header card --}}
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-2xl font-bold text-red-600">৳ {{ number_format($expense->amount, 2) }}</span>
                        <span class="badge-{{ $expense->status === 'active' ? 'active' : 'voided' }}">{{ ucfirst($expense->status) }}</span>
                    </div>
                    <p class="text-gray-700 font-medium">{{ $expense->description }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-calendar text-gray-300 mr-1"></i>{{ $expense->date->format('d F Y') }}
                        &nbsp;·&nbsp;
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs">{{ $expense->category }}</span>
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                    @if($expense->isActive())
                    <form action="{{ route('admin.expenses.void', $expense) }}" method="POST"
                          onsubmit="return confirm('Void this expense?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">
                            <i class="fas fa-ban"></i> Void
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Details --}}
    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm">Payment Details</p></div>
        <div class="card-body grid sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs text-gray-400 font-medium">Payment Method</p>
                <p class="text-sm text-gray-800 mt-0.5 capitalize">{{ $expense->payment_method }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Paid By</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $expense->paid_by ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Recorded By</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $expense->creator?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Recorded On</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $expense->created_at->format('d M Y, h:i A') }}</p>
            </div>
            @if($expense->notes)
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-400 font-medium">Notes</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $expense->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Attachment --}}
    @if($expense->attachment_url)
    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm">Voucher / Attachment</p></div>
        <div class="card-body">
            @if(in_array($ext, ['jpg', 'jpeg', 'png']))
            <img src="{{ $expense->attachment_url }}"
                 alt="Voucher"
                 class="max-w-full rounded-lg border border-gray-200 mb-3"
                 style="max-height: 420px; object-fit: contain;">
            @endif
            <a href="{{ $expense->attachment_url }}" target="_blank"
               class="btn btn-md btn-secondary">
                <i class="fas fa-{{ $ext === 'pdf' ? 'file-pdf text-red-500' : 'image text-blue-500' }}"></i>
                {{ $ext === 'pdf' ? 'Open PDF' : 'Open Image' }}
            </a>
        </div>
    </div>
    @endif

    <a href="{{ route('admin.expenses.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to Expenses
    </a>
</div>
@endsection
