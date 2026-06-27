@extends('layouts.app')
@section('title', 'Edit Additional Info: ' . $member->user->name)
@section('page-title', 'Edit Additional Info')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5 max-w-4xl">

    @if($errors->any())
    <div class="alert-error">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Member context strip --}}
    <div class="flex items-center gap-3">
        <img src="{{ $member->user->photo_url }}" class="w-10 h-10 rounded-xl object-cover border border-gray-200 shrink-0" alt="">
        <div>
            <p class="font-semibold text-gray-900 text-sm">{{ $member->user->name }}</p>
            <p class="text-xs text-gray-400">{{ $member->member_number }}</p>
        </div>
    </div>

    <form action="{{ route('admin.members.additional-info.update', $member) }}" method="POST" class="space-y-5">
        @csrf
        @method('PATCH')

        {{-- Section A: Address Information --}}
        <div class="card">
            <div class="card-header">
                <p class="font-semibold text-gray-800 text-sm"><i class="fas fa-map-marker-alt text-gray-400 mr-1.5"></i> Address Information</p>
            </div>
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label" for="present_address">Present Address</label>
                    <textarea id="present_address" name="present_address" rows="3" class="form-textarea">{{ old('present_address', $member->additionalInfo?->present_address) }}</textarea>
                </div>
                <div>
                    <label class="form-label" for="permanent_address">Permanent Address</label>
                    <textarea id="permanent_address" name="permanent_address" rows="3" class="form-textarea">{{ old('permanent_address', $member->additionalInfo?->permanent_address) }}</textarea>
                </div>
                <div>
                    <label class="form-label" for="business_address">Business / Office Address</label>
                    <textarea id="business_address" name="business_address" rows="3" class="form-textarea">{{ old('business_address', $member->additionalInfo?->business_address) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section B: Emergency Contacts --}}
        <div class="card">
            <div class="card-header">
                <p class="font-semibold text-gray-800 text-sm"><i class="fas fa-phone-alt text-gray-400 mr-1.5"></i> Emergency Contacts</p>
            </div>
            <div class="card-body grid sm:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Primary Contact</p>
                    <div>
                        <label class="form-label" for="primary_emergency_name">Name</label>
                        <input type="text" id="primary_emergency_name" name="primary_emergency_name"
                               class="form-input" maxlength="255"
                               value="{{ old('primary_emergency_name', $member->additionalInfo?->primary_emergency_name) }}">
                    </div>
                    <div>
                        <label class="form-label" for="primary_emergency_relationship">Relationship</label>
                        <input type="text" id="primary_emergency_relationship" name="primary_emergency_relationship"
                               class="form-input" maxlength="100"
                               value="{{ old('primary_emergency_relationship', $member->additionalInfo?->primary_emergency_relationship) }}">
                    </div>
                    <div>
                        <label class="form-label" for="primary_emergency_phone">Phone</label>
                        <input type="text" id="primary_emergency_phone" name="primary_emergency_phone"
                               class="form-input" maxlength="30"
                               value="{{ old('primary_emergency_phone', $member->additionalInfo?->primary_emergency_phone) }}">
                    </div>
                </div>
                <div class="space-y-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Secondary Contact</p>
                    <div>
                        <label class="form-label" for="secondary_emergency_name">Name</label>
                        <input type="text" id="secondary_emergency_name" name="secondary_emergency_name"
                               class="form-input" maxlength="255"
                               value="{{ old('secondary_emergency_name', $member->additionalInfo?->secondary_emergency_name) }}">
                    </div>
                    <div>
                        <label class="form-label" for="secondary_emergency_relationship">Relationship</label>
                        <input type="text" id="secondary_emergency_relationship" name="secondary_emergency_relationship"
                               class="form-input" maxlength="100"
                               value="{{ old('secondary_emergency_relationship', $member->additionalInfo?->secondary_emergency_relationship) }}">
                    </div>
                    <div>
                        <label class="form-label" for="secondary_emergency_phone">Phone</label>
                        <input type="text" id="secondary_emergency_phone" name="secondary_emergency_phone"
                               class="form-input" maxlength="30"
                               value="{{ old('secondary_emergency_phone', $member->additionalInfo?->secondary_emergency_phone) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section C: Personal & Social Information --}}
        <div class="card">
            <div class="card-header">
                <p class="font-semibold text-gray-800 text-sm"><i class="fas fa-user-circle text-gray-400 mr-1.5"></i> Personal &amp; Social Information</p>
            </div>
            <div class="card-body grid sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4">
                <div>
                    <label class="form-label" for="marital_status">Marital Status</label>
                    <select id="marital_status" name="marital_status" class="form-input">
                        <option value="">— Select —</option>
                        @foreach(['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('marital_status', $member->additionalInfo?->marital_status) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="religion">Religion</label>
                    <input type="text" id="religion" name="religion"
                           class="form-input" maxlength="100"
                           value="{{ old('religion', $member->additionalInfo?->religion) }}">
                </div>
                <div>
                    <label class="form-label" for="marriage_anniversary">Marriage Anniversary</label>
                    <input type="date" id="marriage_anniversary" name="marriage_anniversary"
                           class="form-input"
                           value="{{ old('marriage_anniversary', $member->additionalInfo?->marriage_anniversary?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="form-label" for="blood_group">Blood Group</label>
                    <input type="text" id="blood_group" name="blood_group"
                           class="form-input" maxlength="5"
                           placeholder="e.g. A+"
                           value="{{ old('blood_group', $member->additionalInfo?->blood_group) }}">
                </div>
                <div>
                    <label class="form-label" for="nationality">Nationality</label>
                    <input type="text" id="nationality" name="nationality"
                           class="form-input" maxlength="100"
                           value="{{ old('nationality', $member->additionalInfo?->nationality ?? 'Bangladeshi') }}">
                </div>
                <div>
                    <label class="form-label" for="nid_passport">NID / Passport Number</label>
                    <input type="text" id="nid_passport" name="nid_passport"
                           class="form-input" maxlength="50"
                           value="{{ old('nid_passport', $member->additionalInfo?->nid_passport) }}">
                </div>
                <div class="sm:col-span-2 md:col-span-3">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="form-textarea" maxlength="2000">{{ old('notes', $member->additionalInfo?->notes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ route('admin.members.additional-info.show', $member) }}" class="btn btn-sm btn-secondary">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection
