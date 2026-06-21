@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Profile header card --}}
    <div class="card p-6">
        <div class="flex items-center gap-5">
            <div class="relative shrink-0">
                <img src="{{ $user->photo_url }}" alt="Photo"
                     class="w-20 h-20 rounded-2xl object-cover ring-4 ring-gray-100 shadow-md">
                <span class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 border-2 border-white rounded-full"></span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                @if($user->member)
                <p class="text-sm text-gray-500 mt-0.5">
                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $user->member->member_number }}</span>
                    · Member since {{ $user->member->join_date->format('M Y') }}
                </p>
                @endif
                <p class="text-sm text-gray-400 mt-1">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    {{-- Edit form --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Edit Profile</h2>
                <p class="text-xs text-gray-400 mt-0.5">Keep your information up to date</p>
            </div>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert-error mb-5">
                <i class="fas fa-circle-exclamation shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
                </div>
            </div>
            @endif

            <form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PATCH')

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Full Name <span class="form-required">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                               placeholder="01XXXXXXXXX" class="form-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Address</label>
                        <textarea name="address" rows="2" placeholder="Your full address"
                                  class="form-textarea">{{ old('address', $user->address) }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Profession</label>
                        <input type="text" name="profession" value="{{ old('profession', $user->profession) }}"
                               placeholder="e.g. Engineer, Teacher" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Emergency Contact</label>
                        <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $user->emergency_contact) }}"
                               placeholder="Name & phone number" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Nominee Name</label>
                        <input type="text" name="nominee_name" value="{{ old('nominee_name', $user->nominee_name) }}"
                               placeholder="Nominee's full name" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Nominee Contact</label>
                        <input type="text" name="nominee_contact" value="{{ old('nominee_contact', $user->nominee_contact) }}"
                               placeholder="Nominee's phone" class="form-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Update Photo</label>
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg"
                               class="form-input file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="form-hint">JPG or PNG, max 2MB. Leave empty to keep current photo.</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="btn-primary flex-1 justify-center">
                        <i class="fas fa-save"></i> Save Profile
                    </button>
                    <a href="{{ route('member.dashboard') }}" class="btn-secondary flex-1 justify-center">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
