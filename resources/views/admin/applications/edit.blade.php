@extends('layouts.app')
@section('title', 'Edit Application')
@section('page-title', 'Edit Application Data')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <div>
                <p class="font-semibold text-gray-800">{{ $application->full_name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Editing application data — status will not change</p>
            </div>
            <span class="{{ $application->statusClass() }}">{{ $application->statusLabel() }}</span>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert-error mb-4">
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.applications.update', $application) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PATCH')

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Full Name <span class="form-required">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $application->full_name) }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Phone <span class="form-required">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $application->phone) }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $application->email) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="date_of_birth"
                               value="{{ old('date_of_birth', $application->date_of_birth?->format('Y-m-d')) }}" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label">Address <span class="form-required">*</span></label>
                    <textarea name="address" rows="2" required class="form-textarea">{{ old('address', $application->address) }}</textarea>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Profession</label>
                        <input type="text" name="profession" value="{{ old('profession', $application->profession) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Emergency Contact</label>
                        <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $application->emergency_contact) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Nominee Name</label>
                        <input type="text" name="nominee_name" value="{{ old('nominee_name', $application->nominee_name) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Nominee Contact</label>
                        <input type="text" name="nominee_contact" value="{{ old('nominee_contact', $application->nominee_contact) }}" class="form-input">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Requested Monthly Fee (৳)</label>
                        <input type="number" name="monthly_fee_amount"
                               value="{{ old('monthly_fee_amount', $application->monthly_fee_amount) }}"
                               min="0" step="0.01" class="form-input">
                    </div>
                    <div class="flex items-center gap-3 pt-6">
                        <input type="checkbox" name="is_existing_member" id="is_existing" value="1"
                               {{ $application->is_existing_member ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_existing" class="text-sm text-gray-700">Existing member</label>
                    </div>
                    <div>
                        <label class="form-label">Existing Since (if applicable)</label>
                        <input type="date" name="membership_date"
                               value="{{ old('membership_date', $application->membership_date?->format('Y-m-d')) }}" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label">Applicant Notes</label>
                    <textarea name="notes" rows="3" class="form-textarea">{{ old('notes', $application->notes) }}</textarea>
                </div>

                <div>
                    <label class="form-label">Replace Photo</label>
                    @if($application->photo_url)
                    <div class="mb-2">
                        <img src="{{ $application->photo_url }}" class="w-16 h-16 rounded-lg object-cover border border-gray-200" alt="Current photo">
                        <p class="text-xs text-gray-400 mt-1">Current photo — upload a new one to replace</p>
                    </div>
                    @endif
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg" class="form-input text-xs">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <a href="{{ route('admin.applications.show', $application) }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
