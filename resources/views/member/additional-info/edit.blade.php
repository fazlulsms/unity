@extends('layouts.app')
@section('title', 'Edit Additional Information')
@section('page-title', 'Edit Additional Information')
@section('sidebar') @include('partials.member-nav') @endsection

@php $info = $member->additionalInfo; $inp = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none'; @endphp

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        @if($errors->any())
        <div class="alert-error mb-4">
            <i class="fas fa-circle-exclamation text-red-500 mt-0.5 shrink-0"></i>
            <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
        <form action="{{ route('member.additional-info.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Addresses</h3>
                <div class="space-y-4">
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Present Address</label><textarea name="present_address" rows="2" class="{{ $inp }}">{{ old('present_address', $info->present_address ?? '') }}</textarea></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Permanent Address</label><textarea name="permanent_address" rows="2" class="{{ $inp }}">{{ old('permanent_address', $info->permanent_address ?? '') }}</textarea></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Business Address</label><textarea name="business_address" rows="2" class="{{ $inp }}">{{ old('business_address', $info->business_address ?? '') }}</textarea></div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Primary Emergency Contact</h3>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Name</label><input type="text" name="primary_emergency_name" value="{{ old('primary_emergency_name', $info->primary_emergency_name ?? '') }}" class="{{ $inp }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Relationship</label><input type="text" name="primary_emergency_relationship" value="{{ old('primary_emergency_relationship', $info->primary_emergency_relationship ?? '') }}" class="{{ $inp }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Phone</label><input type="text" name="primary_emergency_phone" value="{{ old('primary_emergency_phone', $info->primary_emergency_phone ?? '') }}" class="{{ $inp }}"></div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Secondary Emergency Contact</h3>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Name</label><input type="text" name="secondary_emergency_name" value="{{ old('secondary_emergency_name', $info->secondary_emergency_name ?? '') }}" class="{{ $inp }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Relationship</label><input type="text" name="secondary_emergency_relationship" value="{{ old('secondary_emergency_relationship', $info->secondary_emergency_relationship ?? '') }}" class="{{ $inp }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Phone</label><input type="text" name="secondary_emergency_phone" value="{{ old('secondary_emergency_phone', $info->secondary_emergency_phone ?? '') }}" class="{{ $inp }}"></div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Personal Details</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Marital Status</label>
                        <select name="marital_status" class="{{ $inp }} cursor-pointer">
                            <option value="">—</option>
                            @foreach(['single','married','divorced','widowed'] as $ms)
                            <option value="{{ $ms }}" {{ old('marital_status', $info->marital_status ?? '') === $ms ? 'selected' : '' }}>{{ ucfirst($ms) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Marriage Anniversary</label><input type="date" name="marriage_anniversary" value="{{ old('marriage_anniversary', $info && $info->marriage_anniversary ? $info->marriage_anniversary->format('Y-m-d') : '') }}" class="{{ $inp }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Religion</label><input type="text" name="religion" value="{{ old('religion', $info->religion ?? '') }}" class="{{ $inp }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Blood Group</label><input type="text" name="blood_group" value="{{ old('blood_group', $info->blood_group ?? '') }}" maxlength="5" class="{{ $inp }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">Nationality</label><input type="text" name="nationality" value="{{ old('nationality', $info->nationality ?? '') }}" class="{{ $inp }}"></div>
                    <div><label class="block text-xs font-medium text-gray-700 mb-1">NID / Passport</label><input type="text" name="nid_passport" value="{{ old('nid_passport', $info->nid_passport ?? '') }}" class="{{ $inp }}"></div>
                </div>
            </div>

            <div><label class="block text-xs font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" rows="3" class="{{ $inp }}">{{ old('notes', $info->notes ?? '') }}</textarea></div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm">Save Changes</button>
                <a href="{{ route('member.additional-info.show') }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
