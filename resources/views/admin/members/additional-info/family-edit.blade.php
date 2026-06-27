@extends('layouts.app')
@section('title', 'Edit Family Member: ' . $family->name)
@section('page-title', 'Edit Family Member')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5 max-w-3xl">

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

    <form action="{{ route('admin.members.family.update', [$member, $family]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="card">
            <div class="card-header">
                <p class="font-semibold text-gray-800 text-sm"><i class="fas fa-user-edit text-gray-400 mr-1.5"></i> Edit: {{ $family->name }}</p>
            </div>
            <div class="card-body space-y-4">

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label" for="type">Relationship Type <span class="form-required">*</span></label>
                        <select id="type" name="type" class="form-input" onchange="toggleRelationship(this.value)" required>
                            <option value="">— Select —</option>
                            @foreach(['spouse' => 'Spouse', 'child' => 'Child', 'father' => 'Father', 'mother' => 'Mother', 'sibling' => 'Sibling', 'other' => 'Other'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('type', $family->type) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="relationship_wrap" style="{{ old('type', $family->type) === 'other' ? '' : 'display:none' }}">
                        <label class="form-label" for="relationship">Custom Relationship Label</label>
                        <input type="text" id="relationship" name="relationship"
                               class="form-input" maxlength="100"
                               placeholder="e.g. Uncle, Aunt..."
                               value="{{ old('relationship', $family->relationship) }}">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label" for="name">Full Name <span class="form-required">*</span></label>
                        <input type="text" id="name" name="name"
                               class="form-input" maxlength="255" required
                               value="{{ old('name', $family->name) }}">
                    </div>
                    <div>
                        <label class="form-label" for="sex">Sex</label>
                        <select id="sex" name="sex" class="form-input">
                            <option value="">— Select —</option>
                            @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('sex', $family->sex) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label" for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth"
                               class="form-input"
                               value="{{ old('date_of_birth', $family->date_of_birth?->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="form-label" for="profession">Profession</label>
                        <input type="text" id="profession" name="profession"
                               class="form-input" maxlength="255"
                               value="{{ old('profession', $family->profession) }}">
                    </div>
                    <div>
                        <label class="form-label" for="organization">Organization</label>
                        <input type="text" id="organization" name="organization"
                               class="form-input" maxlength="255"
                               value="{{ old('organization', $family->organization) }}">
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label" for="phone">Phone</label>
                        <input type="text" id="phone" name="phone"
                               class="form-input" maxlength="30"
                               value="{{ old('phone', $family->phone) }}">
                    </div>
                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input type="email" id="email" name="email"
                               class="form-input" maxlength="255"
                               value="{{ old('email', $family->email) }}">
                    </div>
                </div>

                {{-- Current photo --}}
                @if($family->photo_url)
                <div>
                    <p class="form-label">Current Photo</p>
                    <img src="{{ $family->photo_url }}" class="w-20 h-20 rounded-xl object-cover border border-gray-200 mt-1" alt="">
                </div>
                @endif

                <div>
                    <label class="form-label" for="photo">{{ $family->photo_url ? 'Replace Photo' : 'Photo' }} <span class="text-xs text-gray-400">(optional, JPEG/PNG, max 2MB)</span></label>
                    <input type="file" id="photo" name="photo"
                           class="form-input" accept="image/jpeg,image/png,image/jpg">
                </div>

                <div>
                    <label class="form-label" for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="form-textarea" maxlength="1000">{{ old('notes', $family->notes) }}</textarea>
                </div>

            </div>
        </div>

        <div class="flex gap-3 mt-5">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ route('admin.members.additional-info.show', $member) }}" class="btn btn-sm btn-secondary">
                Cancel
            </a>
        </div>

    </form>
</div>

<script>
function toggleRelationship(val) {
    const wrap = document.getElementById('relationship_wrap');
    wrap.style.display = val === 'other' ? '' : 'none';
    if (val !== 'other') {
        document.getElementById('relationship').value = '';
    }
}
</script>
@endsection
