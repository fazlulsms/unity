@extends('layouts.app')
@section('title', 'Edit Member Profile')
@section('page-title', 'Edit Member Profile')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-2xl space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    <div class="card">
        <div class="card-header">
            <div>
                <p class="font-semibold text-gray-800">{{ $member->user->name }}</p>
                <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $member->member_number }}</p>
            </div>
            <span class="badge-{{ $member->status }}">{{ ucfirst($member->status) }}</span>
        </div>
        <div class="card-body">

            @if($errors->any())
            <div class="alert-error mb-4">
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.members.update', $member) }}" method="POST"
                  enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- Personal Information --}}
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Personal Information</p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Full Name <span class="form-required">*</span></label>
                            <input type="text" name="name"
                                   value="{{ old('name', $member->user->name) }}"
                                   required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone"
                                   value="{{ old('phone', $member->user->phone) }}"
                                   class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth"
                                   value="{{ old('date_of_birth', $member->user->date_of_birth?->format('Y-m-d')) }}"
                                   class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Profession</label>
                            <input type="text" name="profession"
                                   value="{{ old('profession', $member->user->profession) }}"
                                   class="form-input">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="2" class="form-textarea">{{ old('address', $member->user->address) }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Emergency Contact</label>
                            <input type="text" name="emergency_contact"
                                   value="{{ old('emergency_contact', $member->user->emergency_contact) }}"
                                   class="form-input">
                        </div>
                    </div>
                </div>

                {{-- Nominee --}}
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Nominee Information</p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Nominee Name</label>
                            <input type="text" name="nominee_name"
                                   value="{{ old('nominee_name', $member->user->nominee_name) }}"
                                   class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Nominee Contact</label>
                            <input type="text" name="nominee_contact"
                                   value="{{ old('nominee_contact', $member->user->nominee_contact) }}"
                                   class="form-input">
                        </div>
                    </div>
                </div>

                {{-- Profile Photo --}}
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Profile Photo</p>
                    @if($member->user->photo_url)
                    <div class="flex items-center gap-4 mb-3">
                        <img src="{{ $member->user->photo_url }}"
                             class="w-16 h-16 rounded-xl object-cover border border-gray-200" alt="">
                        <p class="text-xs text-gray-400">Upload a new photo to replace the current one.</p>
                    </div>
                    @endif
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg"
                           class="form-input text-xs py-1.5">
                    <p class="text-xs text-gray-400 mt-1">JPEG or PNG, max 2 MB.</p>
                </div>

                {{-- Membership Settings --}}
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Membership Settings</p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Monthly Fee (৳) <span class="form-required">*</span></label>
                            <input type="number" name="monthly_fee_amount"
                                   value="{{ old('monthly_fee_amount', $member->monthly_fee_amount) }}"
                                   required min="0" step="0.01" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Join Date <span class="form-required">*</span></label>
                            <input type="date" name="join_date"
                                   value="{{ old('join_date', $member->join_date->format('Y-m-d')) }}"
                                   required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Status <span class="form-required">*</span></label>
                            <select name="status" required class="form-input">
                                @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $v => $l)
                                <option value="{{ $v }}" {{ old('status', $member->status) === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="form-label">Internal Notes</label>
                            <textarea name="notes" rows="2" class="form-textarea">{{ old('notes', $member->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <a href="{{ route('admin.members.show', $member) }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
