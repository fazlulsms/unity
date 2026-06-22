@extends('layouts.app')
@section('title', 'Review Application')
@section('page-title', 'Review Application')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-4xl space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    {{-- Header card --}}
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row items-start gap-5">
                {{-- Photo --}}
                @if($application->photo_url)
                    <img src="{{ $application->photo_url }}" alt="Photo"
                         class="w-24 h-24 rounded-xl object-cover border border-gray-200 shrink-0">
                @else
                    <div class="w-24 h-24 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-user text-gray-400 text-3xl"></i>
                    </div>
                @endif
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-1">
                        <h2 class="text-xl font-bold text-gray-900">{{ $application->full_name }}</h2>
                        <span class="{{ $application->statusClass() }}">{{ $application->statusLabel() }}</span>
                    </div>
                    <p class="text-gray-500 text-sm">{{ $application->phone }}
                        @if($application->email) · {{ $application->email }} @endif
                    </p>
                    <p class="text-gray-400 text-xs mt-1">Applied {{ $application->created_at->format('d M Y') }}</p>
                    @if($application->reviewed_at)
                    <p class="text-gray-400 text-xs mt-0.5">
                        Last action by {{ $application->reviewer?->name ?? 'System' }}
                        on {{ $application->reviewed_at->format('d M Y') }}
                    </p>
                    @endif
                </div>
                <a href="{{ route('admin.applications.edit', $application) }}" class="btn btn-sm btn-secondary shrink-0">
                    <i class="fas fa-pen"></i> Edit Data
                </a>
            </div>
        </div>
    </div>

    <div class="flex flex-col xl:flex-row gap-5">

        {{-- Submitted details --}}
        <div class="flex-1 min-w-0 space-y-5">
            <div class="card">
                <div class="card-header">
                    <p class="font-semibold text-gray-800 text-sm">Submitted Details</p>
                </div>
                <div class="card-body grid sm:grid-cols-2 gap-x-8 gap-y-4">
                    @foreach([
                        'Address'           => $application->address,
                        'Date of Birth'     => $application->date_of_birth?->format('d M Y') ?? '—',
                        'Profession'        => $application->profession ?: '—',
                        'Emergency Contact' => $application->emergency_contact ?: '—',
                        'Nominee Name'      => $application->nominee_name ?: '—',
                        'Nominee Contact'   => $application->nominee_contact ?: '—',
                        'Existing Member'   => $application->is_existing_member ? 'Yes' : 'No',
                        'Existing Since'    => $application->membership_date?->format('d M Y') ?? '—',
                        'Requested Monthly Fee' => '৳ ' . number_format($application->monthly_fee_amount, 2),
                    ] as $label => $value)
                    <div>
                        <p class="text-xs text-gray-400 font-medium">{{ $label }}</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ $value }}</p>
                    </div>
                    @endforeach
                </div>

                @if($application->notes)
                <div class="px-6 pb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-400 font-medium mb-1">Applicant Notes</p>
                        <p class="text-sm text-gray-700">{{ $application->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Approved member record --}}
            @if($application->isApproved() && $application->member)
            <div class="card">
                <div class="card-header">
                    <p class="font-semibold text-emerald-700 text-sm"><i class="fas fa-check-circle mr-1"></i> Approved Member Record</p>
                    <a href="{{ route('admin.members.show', $application->member) }}" class="btn btn-sm btn-success">View Member</a>
                </div>
                <div class="card-body grid sm:grid-cols-3 gap-4">
                    <div><p class="text-xs text-gray-400 font-medium">Member ID</p>
                         <p class="text-sm font-bold text-gray-900">{{ $application->member->member_number }}</p></div>
                    <div><p class="text-xs text-gray-400 font-medium">Join Date</p>
                         <p class="text-sm text-gray-800">{{ $application->member->join_date->format('d M Y') }}</p></div>
                    <div><p class="text-xs text-gray-400 font-medium">Monthly Fee</p>
                         <p class="text-sm text-gray-800">৳ {{ number_format($application->member->monthly_fee_amount, 2) }}</p></div>
                </div>
            </div>
            @endif

            {{-- Rejection info --}}
            @if($application->isRejected())
            <div class="card border-red-100">
                <div class="card-body">
                    <p class="text-sm font-semibold text-red-700 mb-1">Application Rejected</p>
                    <p class="text-sm text-gray-700">{{ $application->rejection_reason }}</p>
                    @if($application->review_remarks)
                    <p class="text-xs text-gray-500 mt-2">Remarks: {{ $application->review_remarks }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Action panel --}}
        <div class="xl:w-80 shrink-0 space-y-4">

            @if($application->isOpen())
            {{-- Quick status actions --}}
            <div class="card">
                <div class="card-header"><p class="font-semibold text-gray-800 text-sm">Change Status</p></div>
                <div class="card-body space-y-2">
                    <form action="{{ route('admin.applications.under-review', $application) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-md w-full bg-sky-600 text-white hover:bg-sky-700 shadow-sm focus:ring-sky-500">
                            <i class="fas fa-search"></i> Mark Under Review
                        </button>
                    </form>
                    <form action="{{ route('admin.applications.photo-required', $application) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-md w-full bg-violet-600 text-white hover:bg-violet-700 shadow-sm focus:ring-violet-500">
                            <i class="fas fa-camera"></i> Ask for Photo
                        </button>
                    </form>
                    <form action="{{ route('admin.applications.more-info', $application) }}" method="POST" class="space-y-2">
                        @csrf
                        <input type="text" name="note" placeholder="What info is needed? (optional)"
                               class="form-input text-xs">
                        <button type="submit" class="btn btn-md w-full bg-orange-500 text-white hover:bg-orange-600 shadow-sm focus:ring-orange-400">
                            <i class="fas fa-question-circle"></i> Ask More Information
                        </button>
                    </form>
                </div>
            </div>

            {{-- Approve --}}
            <div class="card border-emerald-100">
                <div class="card-header bg-emerald-50/50">
                    <p class="font-semibold text-emerald-800 text-sm"><i class="fas fa-check-circle mr-1"></i> Approve</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.applications.approve', $application) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="form-label">Monthly Fee (৳) <span class="form-required">*</span></label>
                            <input type="number" name="monthly_fee_amount"
                                   value="{{ $application->monthly_fee_amount }}"
                                   required min="0" step="0.01" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Join Date <span class="form-required">*</span></label>
                            <input type="date" name="join_date"
                                   value="{{ now()->format('Y-m-d') }}"
                                   required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Remarks (optional)</label>
                            <textarea name="review_remarks" rows="2" class="form-textarea"></textarea>
                        </div>
                        <button type="submit" class="btn-success w-full"
                            onclick="return confirm('Approve this application and create a member account?')">
                            <i class="fas fa-check"></i> Approve & Create Account
                        </button>
                    </form>
                </div>
            </div>

            {{-- Reject --}}
            <div class="card border-red-100">
                <div class="card-header bg-red-50/50">
                    <p class="font-semibold text-red-800 text-sm"><i class="fas fa-times-circle mr-1"></i> Reject</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.applications.reject', $application) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="form-label">Rejection Reason <span class="form-required">*</span></label>
                            <textarea name="rejection_reason" rows="3" required class="form-textarea"></textarea>
                        </div>
                        <button type="submit" class="btn-danger w-full"
                            onclick="return confirm('Reject this application?')">
                            <i class="fas fa-times"></i> Reject Application
                        </button>
                    </form>
                </div>
            </div>
            @endif

            {{-- Internal notes --}}
            <div class="card">
                <div class="card-header">
                    <p class="font-semibold text-gray-800 text-sm"><i class="fas fa-lock text-gray-400 mr-1"></i> Internal Notes</p>
                    <span class="text-xs text-gray-400">Admin only</span>
                </div>
                <div class="card-body space-y-3">
                    @if($application->internal_notes)
                    <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                        <pre class="text-xs text-gray-700 whitespace-pre-wrap font-sans leading-relaxed">{{ $application->internal_notes }}</pre>
                    </div>
                    @else
                    <p class="text-xs text-gray-400 italic">No internal notes yet.</p>
                    @endif
                    <form action="{{ route('admin.applications.note', $application) }}" method="POST" class="space-y-2">
                        @csrf
                        <textarea name="note" rows="2" required placeholder="Add internal note…" class="form-textarea text-xs"></textarea>
                        <button type="submit" class="btn btn-sm btn-secondary w-full">
                            <i class="fas fa-plus"></i> Add Note
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.applications.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to applications
    </a>
</div>
@endsection
