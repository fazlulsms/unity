@php
    $inp = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none';
    $assigned = $assigned ?? [];
@endphp
@if($errors->any())
<div class="alert-error mb-4">
    <i class="fas fa-circle-exclamation text-red-500 mt-0.5 shrink-0"></i>
    <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<div class="grid sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-xs font-medium text-gray-700 mb-1">Booster Contribution Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $drive->title ?? '') }}" required placeholder="e.g. Eid 2026 Special Contribution" class="{{ $inp }}">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Contribution Date / Period <span class="text-red-500">*</span></label>
        <input type="date" name="period_date" value="{{ old('period_date', isset($drive) ? $drive->period_date->format('Y-m-d') : date('Y-m-d')) }}" required class="{{ $inp }}">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Expected Amount per Member (৳) <span class="text-red-500">*</span></label>
        <input type="number" name="expected_amount_per_member" value="{{ old('expected_amount_per_member', $drive->expected_amount_per_member ?? '') }}" min="0" step="0.01" required class="{{ $inp }}">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select name="status" class="{{ $inp }} cursor-pointer">
            @foreach(['active' => 'Active', 'closed' => 'Closed'] as $val => $label)
            <option value="{{ $val }}" {{ old('status', $drive->status ?? 'active') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-xs font-medium text-gray-700 mb-1">Remarks</label>
        <textarea name="remarks" rows="2" class="{{ $inp }}">{{ old('remarks', $drive->remarks ?? '') }}</textarea>
    </div>
</div>

<div x-data="{ all: false }">
    <div class="flex items-center justify-between mb-2">
        <label class="block text-xs font-medium text-gray-700">Applicable Members <span class="text-red-500">*</span></label>
        <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
            <input type="checkbox" x-model="all" @change="$root.querySelectorAll('.member-cb').forEach(c => c.checked = all)" class="w-4 h-4 rounded border-gray-300">
            Select all active
        </label>
    </div>
    <div class="border border-gray-200 rounded-lg max-h-64 overflow-y-auto divide-y divide-gray-50">
        @forelse($members as $m)
        <label class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 cursor-pointer">
            <input type="checkbox" name="members[]" value="{{ $m->id }}" class="member-cb w-4 h-4 rounded border-gray-300"
                {{ collect(old('members', $assigned))->contains($m->id) ? 'checked' : '' }}>
            <span class="text-sm text-gray-800">{{ $m->user->name ?? 'Unknown' }}</span>
            <span class="text-xs text-gray-400 font-mono ml-auto">{{ $m->member_number }}</span>
        </label>
        @empty
        <p class="px-3 py-4 text-sm text-gray-400">No active members.</p>
        @endforelse
    </div>
    <p class="mt-1 text-xs text-gray-400">Each selected member is expected to contribute the per-member amount above.</p>
</div>
