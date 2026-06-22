@extends('layouts.app')
@section('title', 'Income #' . $income->id)
@section('page-title', 'Income Details')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
@php $ext = $income->attachment ? strtolower(pathinfo($income->attachment, PATHINFO_EXTENSION)) : null; @endphp
<div class="max-w-2xl space-y-5">

    {{-- Header card --}}
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-2xl font-bold text-emerald-600">৳ {{ number_format($income->amount, 2) }}</span>
                        <span class="badge-{{ $income->status === 'active' ? 'active' : 'voided' }}">{{ ucfirst($income->status) }}</span>
                        <span class="bg-teal-100 text-teal-700 px-2 py-0.5 rounded text-xs font-medium">{{ $income->income_type_label }}</span>
                    </div>
                    <p class="text-gray-700 font-medium">{{ $income->source }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-calendar text-gray-300 mr-1"></i>{{ $income->date->format('d F Y') }}
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('admin.income.edit', $income) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                    @if($income->isActive())
                    <form action="{{ route('admin.income.void', $income) }}" method="POST"
                          onsubmit="return confirm('Void this income entry?')">
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
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm">Income Details</p></div>
        <div class="card-body grid sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs text-gray-400 font-medium">Source</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $income->source }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Reference</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $income->reference ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Recorded By</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $income->creator?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Recorded On</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $income->created_at->format('d M Y, h:i A') }}</p>
            </div>
            @if($income->notes)
            <div class="sm:col-span-2">
                <p class="text-xs text-gray-400 font-medium">Notes</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $income->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Attachment --}}
    @if($income->attachment_url)
    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm">Supporting Document</p></div>
        <div class="card-body">
            @if(in_array($ext, ['jpg', 'jpeg', 'png']))
            <img src="{{ $income->attachment_url }}"
                 alt="Document"
                 class="max-w-full rounded-lg border border-gray-200 mb-3"
                 style="max-height: 420px; object-fit: contain;">
            @endif
            <a href="{{ $income->attachment_url }}" target="_blank"
               class="btn btn-md btn-secondary">
                <i class="fas fa-{{ $ext === 'pdf' ? 'file-pdf text-red-500' : 'image text-blue-500' }}"></i>
                {{ $ext === 'pdf' ? 'Open PDF' : 'Open Image' }}
            </a>
        </div>
    </div>
    @endif

    <a href="{{ route('admin.income.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to Income
    </a>
</div>
@endsection
