@extends('layouts.app')
@section('title', 'Additional Info: ' . $member->user->name)
@section('page-title', 'Extended Member Profile')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    {{-- Header card --}}
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row items-start gap-4">
                <img src="{{ $member->user->photo_url }}"
                     class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0" alt="">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-0.5">
                        <h2 class="text-lg font-bold text-gray-900">{{ $member->user->name }}</h2>
                        <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $member->member_number }}</span>
                        <span class="badge-{{ $member->status }}">{{ ucfirst($member->status) }}</span>
                    </div>
                    <p class="text-sm text-gray-500">Extended profile &amp; family information</p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="{{ route('admin.members.show', $member) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Profile
                    </a>
                    <a href="{{ route('admin.members.additional-info.edit', $member) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-pen"></i> Edit Additional Info
                    </a>
                    <a href="{{ route('admin.members.family.create', $member) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus"></i> Add Family Member
                    </a>
                </div>
            </div>
        </div>
    </div>

    @php
        $info = $member->additionalInfo;
        $family = $member->familyMembers;
        $hasAny = $info || $family->isNotEmpty();
    @endphp

    @if(!$hasAny)
    <div class="card">
        <div class="card-body text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-address-card text-gray-400 text-2xl"></i>
            </div>
            <p class="text-sm font-semibold text-gray-600 mb-1">No extended profile yet</p>
            <p class="text-xs text-gray-400 mb-4">Fill in additional information like addresses, emergency contacts, and family members.</p>
            <a href="{{ route('admin.members.additional-info.edit', $member) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-pen"></i> Fill in Additional Info
            </a>
        </div>
    </div>
    @endif

    @if($info)

    {{-- A. Address Information --}}
    @if($info->present_address || $info->permanent_address || $info->business_address)
    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm"><i class="fas fa-map-marker-alt text-gray-400 mr-1.5"></i> Address Information</p></div>
        <div class="card-body grid sm:grid-cols-3 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs text-gray-400 font-medium">Present Address</p>
                <p class="text-sm text-gray-800 mt-0.5 whitespace-pre-line">{{ $info->present_address ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Permanent Address</p>
                <p class="text-sm text-gray-800 mt-0.5 whitespace-pre-line">{{ $info->permanent_address ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Business / Office Address</p>
                <p class="text-sm text-gray-800 mt-0.5 whitespace-pre-line">{{ $info->business_address ?: '—' }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- B. Emergency Contacts --}}
    @if($info->primary_emergency_name || $info->secondary_emergency_name)
    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm"><i class="fas fa-phone-alt text-gray-400 mr-1.5"></i> Emergency Contacts</p></div>
        <div class="card-body grid sm:grid-cols-2 gap-6">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Primary</p>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Name</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ $info->primary_emergency_name ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Relationship</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ $info->primary_emergency_relationship ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Phone</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ $info->primary_emergency_phone ?: '—' }}</p>
                    </div>
                </div>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Secondary</p>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Name</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ $info->secondary_emergency_name ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Relationship</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ $info->secondary_emergency_relationship ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Phone</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ $info->secondary_emergency_phone ?: '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- C. Personal & Social Information --}}
    @if($info->marital_status || $info->religion || $info->marriage_anniversary || $info->blood_group || $info->nationality || $info->nid_passport || $info->notes)
    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm"><i class="fas fa-user-circle text-gray-400 mr-1.5"></i> Personal &amp; Social Information</p></div>
        <div class="card-body grid sm:grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs text-gray-400 font-medium">Marital Status</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $info->marital_status ? ucfirst($info->marital_status) : '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Religion</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $info->religion ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Marriage Anniversary</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $info->marriage_anniversary?->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Blood Group</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $info->blood_group ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Nationality</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $info->nationality ?: '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">NID / Passport</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $info->nid_passport ?: '—' }}</p>
            </div>
            @if($info->notes)
            <div class="sm:col-span-2 md:col-span-3">
                <p class="text-xs text-gray-400 font-medium">Notes</p>
                <p class="text-sm text-gray-800 mt-0.5 whitespace-pre-line">{{ $info->notes }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    @endif {{-- end if $info --}}

    {{-- D. Spouse --}}
    @php $spouse = $family->where('type', 'spouse')->first(); @endphp
    <div class="card">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm"><i class="fas fa-heart text-gray-400 mr-1.5"></i> Spouse</p>
        </div>
        @if($spouse)
        <div class="card-body flex items-start gap-4">
            @if($spouse->photo_url)
            <img src="{{ $spouse->photo_url }}" class="w-16 h-16 rounded-xl object-cover border border-gray-200 shrink-0" alt="">
            @else
            <div class="w-16 h-16 rounded-xl bg-pink-100 flex items-center justify-center shrink-0">
                <i class="fas fa-user text-pink-400 text-xl"></i>
            </div>
            @endif
            <div class="flex-1 grid sm:grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-3">
                <div>
                    <p class="text-xs text-gray-400 font-medium">Name</p>
                    <p class="text-sm text-gray-800 mt-0.5 font-semibold">{{ $spouse->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Sex</p>
                    <p class="text-sm text-gray-800 mt-0.5">{{ $spouse->sex ? ucfirst($spouse->sex) : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Date of Birth</p>
                    <p class="text-sm text-gray-800 mt-0.5">{{ $spouse->date_of_birth?->format('d M Y') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Profession</p>
                    <p class="text-sm text-gray-800 mt-0.5">{{ $spouse->profession ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Organization</p>
                    <p class="text-sm text-gray-800 mt-0.5">{{ $spouse->organization ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-medium">Phone</p>
                    <p class="text-sm text-gray-800 mt-0.5">{{ $spouse->phone ?: '—' }}</p>
                </div>
                @if($spouse->email)
                <div>
                    <p class="text-xs text-gray-400 font-medium">Email</p>
                    <p class="text-sm text-gray-800 mt-0.5">{{ $spouse->email }}</p>
                </div>
                @endif
            </div>
            <div class="flex flex-col gap-2 shrink-0">
                <a href="{{ route('admin.members.family.edit', [$member, $spouse]) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-pen"></i> Edit
                </a>
                <form action="{{ route('admin.members.family.destroy', [$member, $spouse]) }}" method="POST"
                      onsubmit="return confirm('Remove this spouse record?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger w-full">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="card-body text-center py-6">
            <p class="text-sm text-gray-400">No spouse record. <a href="{{ route('admin.members.family.create', $member) }}" class="text-blue-600 hover:underline">Add Family Member</a></p>
        </div>
        @endif
    </div>

    {{-- E. Children --}}
    @php $children = $family->where('type', 'child'); @endphp
    @if($children->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm"><i class="fas fa-child text-gray-400 mr-1.5"></i> Children ({{ $children->count() }})</p>
            <a href="{{ route('admin.members.family.create', $member) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-plus"></i> Add
            </a>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($children as $child)
            <div class="card-body flex items-center gap-4">
                @if($child->photo_url)
                <img src="{{ $child->photo_url }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0" alt="">
                @else
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-child text-blue-400"></i>
                </div>
                @endif
                <div class="flex-1 grid sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-1">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $child->name }}</p>
                        <p class="text-xs text-gray-400">{{ $child->sex ? ucfirst($child->sex) : '' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Date of Birth</p>
                        <p class="text-sm text-gray-800">{{ $child->date_of_birth?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Profession</p>
                        <p class="text-sm text-gray-800">{{ $child->profession ?: '—' }}</p>
                    </div>
                    @if($child->organization)
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Organization</p>
                        <p class="text-sm text-gray-800">{{ $child->organization }}</p>
                    </div>
                    @endif
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('admin.members.family.edit', [$member, $child]) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-pen"></i>
                    </a>
                    <form action="{{ route('admin.members.family.destroy', [$member, $child]) }}" method="POST"
                          onsubmit="return confirm('Remove this family member?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- F. Other Family Members --}}
    @php $others = $family->whereNotIn('type', ['spouse', 'child']); @endphp
    @if($others->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm"><i class="fas fa-users text-gray-400 mr-1.5"></i> Other Family Members ({{ $others->count() }})</p>
            <a href="{{ route('admin.members.family.create', $member) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-plus"></i> Add
            </a>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($others as $fm)
            <div class="card-body flex items-center gap-4">
                @if($fm->photo_url)
                <img src="{{ $fm->photo_url }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0" alt="">
                @else
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center shrink-0">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                @endif
                <div class="flex-1 grid sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-1">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $fm->name }}</p>
                        <p class="text-xs text-gray-400">{{ $fm->relationship_label }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Date of Birth</p>
                        <p class="text-sm text-gray-800">{{ $fm->date_of_birth?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Profession</p>
                        <p class="text-sm text-gray-800">{{ $fm->profession ?: '—' }}</p>
                    </div>
                    @if($fm->phone)
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Phone</p>
                        <p class="text-sm text-gray-800">{{ $fm->phone }}</p>
                    </div>
                    @endif
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('admin.members.family.edit', [$member, $fm]) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-pen"></i>
                    </a>
                    <form action="{{ route('admin.members.family.destroy', [$member, $fm]) }}" method="POST"
                          onsubmit="return confirm('Remove this family member?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <a href="{{ route('admin.members.show', $member) }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to member profile
    </a>
</div>
@endsection
