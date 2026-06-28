@extends('layouts.app')
@section('title', 'Additional Information')
@section('page-title', 'My Additional Information')
@section('sidebar') @include('partials.member-nav') @endsection

@php
    $info = $member->additionalInfo;
    $row = fn($label, $value) => ['label' => $label, 'value' => $value];
    $sections = [
        'Addresses' => [
            ['Present Address', $info->present_address ?? null],
            ['Permanent Address', $info->permanent_address ?? null],
            ['Business Address', $info->business_address ?? null],
        ],
        'Primary Emergency Contact' => [
            ['Name', $info->primary_emergency_name ?? null],
            ['Relationship', $info->primary_emergency_relationship ?? null],
            ['Phone', $info->primary_emergency_phone ?? null],
        ],
        'Secondary Emergency Contact' => [
            ['Name', $info->secondary_emergency_name ?? null],
            ['Relationship', $info->secondary_emergency_relationship ?? null],
            ['Phone', $info->secondary_emergency_phone ?? null],
        ],
        'Personal Details' => [
            ['Marital Status', $info && $info->marital_status ? ucfirst($info->marital_status) : null],
            ['Marriage Anniversary', $info && $info->marriage_anniversary ? $info->marriage_anniversary->format('d M Y') : null],
            ['Religion', $info->religion ?? null],
            ['Blood Group', $info->blood_group ?? null],
            ['Nationality', $info->nationality ?? null],
            ['NID / Passport', $info->nid_passport ?? null],
        ],
    ];
@endphp

@section('content')
<div class="max-w-4xl space-y-5">

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">Keep your contact and personal details up to date.</p>
        <a href="{{ route('member.additional-info.edit') }}" class="btn-primary btn-sm"><i class="fas fa-pen"></i> Edit</a>
    </div>

    @unless($info)
    <div class="alert-info">
        <i class="fas fa-circle-info text-blue-500 mt-0.5 shrink-0"></i>
        <span>You haven't added any additional information yet. <a href="{{ route('member.additional-info.edit') }}" class="underline font-semibold">Add it now</a>.</span>
    </div>
    @endunless

    @foreach($sections as $title => $fields)
    <div class="card">
        <div class="card-header"><h2 class="text-sm font-semibold text-gray-900">{{ $title }}</h2></div>
        <div class="card-body grid sm:grid-cols-2 gap-x-8 gap-y-4">
            @foreach($fields as $f)
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">{{ $f[0] }}</p>
                <p class="text-sm text-gray-800 mt-0.5">{{ $f[1] ?: '—' }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($info && $info->notes)
    <div class="card">
        <div class="card-header"><h2 class="text-sm font-semibold text-gray-900">Notes</h2></div>
        <div class="card-body"><p class="text-sm text-gray-700 whitespace-pre-line">{{ $info->notes }}</p></div>
    </div>
    @endif

    {{-- Family members (read-only here; managed by admin) --}}
    @if($member->familyMembers->isNotEmpty())
    <div class="card">
        <div class="card-header"><h2 class="text-sm font-semibold text-gray-900">Family Members</h2></div>
        <div class="divide-y divide-gray-50">
            @foreach($member->familyMembers as $fam)
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center shrink-0"><i class="fas fa-user text-gray-400 text-xs"></i></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $fam->name }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ $fam->relationship ?: $fam->type }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-5 py-3 border-t border-gray-50"><p class="text-xs text-gray-400">Family records are managed by the club admin.</p></div>
    </div>
    @endif
</div>
@endsection
