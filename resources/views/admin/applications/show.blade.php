@extends('layouts.app')
@section('title', 'Review Application')
@section('page-title', 'Review Application')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start gap-5 mb-6 pb-6 border-b border-gray-100">
            @if($application->photo)
            <img src="{{ $application->photo_url }}" alt="Photo" class="w-20 h-20 rounded-xl object-cover border border-gray-200">
            @else
            <div class="w-20 h-20 rounded-xl bg-gray-100 flex items-center justify-center">
                <i class="fas fa-user text-gray-400 text-2xl"></i>
            </div>
            @endif
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-xl font-bold text-gray-900">{{ $application->full_name }}</h2>
                    <span class="badge-{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                </div>
                <p class="text-gray-500 text-sm">{{ $application->phone }} · {{ $application->email }}</p>
                <p class="text-gray-400 text-xs mt-1">Applied {{ $application->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-5 mb-6">
            @foreach([
                ['label' => 'Address', 'value' => $application->address],
                ['label' => 'Date of Birth', 'value' => $application->date_of_birth?->format('d M Y') ?? '—'],
                ['label' => 'Profession', 'value' => $application->profession ?: '—'],
                ['label' => 'Emergency Contact', 'value' => $application->emergency_contact ?: '—'],
                ['label' => 'Nominee Name', 'value' => $application->nominee_name ?: '—'],
                ['label' => 'Nominee Contact', 'value' => $application->nominee_contact ?: '—'],
                ['label' => 'Existing Member', 'value' => $application->is_existing_member ? 'Yes' : 'No'],
                ['label' => 'Requested Monthly Fee', 'value' => '৳' . number_format($application->monthly_fee_amount, 2)],
            ] as [$label, $value])
            <div>
                <p class="text-xs text-gray-400 font-medium">{{ $label }}</p>
                <p class="text-sm text-gray-900 mt-0.5">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        @if($application->notes)
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-xs text-gray-400 font-medium mb-1">Applicant Notes</p>
            <p class="text-sm text-gray-700">{{ $application->notes }}</p>
        </div>
        @endif

        @if($application->isPending())
        <div class="grid sm:grid-cols-2 gap-4">
            {{-- Approve --}}
            <div class="border border-green-200 rounded-xl p-4">
                <h3 class="font-semibold text-green-800 mb-3 text-sm">Approve Application</h3>
                <form action="{{ route('admin.applications.approve', $application) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Monthly Fee (৳)</label>
                        <input type="number" name="monthly_fee_amount" value="{{ $application->monthly_fee_amount }}" required min="0" step="0.01"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Join Date</label>
                        <input type="date" name="join_date" value="{{ $application->membership_date?->format('Y-m-d') ?? date('Y-m-d') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Remarks (optional)</label>
                        <textarea name="review_remarks" rows="2"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg font-medium hover:bg-green-700 transition-colors text-sm">
                        Approve & Create Account
                    </button>
                </form>
            </div>

            {{-- Reject --}}
            <div class="border border-red-200 rounded-xl p-4">
                <h3 class="font-semibold text-red-800 mb-3 text-sm">Reject Application</h3>
                <form action="{{ route('admin.applications.reject', $application) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Rejection Reason <span class="text-red-500">*</span></label>
                        <textarea name="rejection_reason" rows="5" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg font-medium hover:bg-red-700 transition-colors text-sm"
                        onclick="return confirm('Reject this application?')">
                        Reject Application
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm font-semibold text-gray-800">
                {{ ucfirst($application->status) }} by {{ $application->reviewer?->name ?? 'System' }}
                on {{ $application->reviewed_at?->format('d M Y') }}
            </p>
            @if($application->rejection_reason)
            <p class="text-sm text-red-600 mt-1">Reason: {{ $application->rejection_reason }}</p>
            @endif
            @if($application->review_remarks)
            <p class="text-sm text-gray-600 mt-1">Remarks: {{ $application->review_remarks }}</p>
            @endif
        </div>
        @endif
    </div>

    <a href="{{ route('admin.applications.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">← Back to applications</a>
</div>
@endsection
