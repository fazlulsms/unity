@extends('layouts.app')
@section('title', 'Edit Member')
@section('page-title', 'Edit Member')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h2 class="font-semibold text-gray-900 mb-5">{{ $member->user->name }} — Edit</h2>
        <form action="{{ route('admin.members.update', $member) }}" method="POST" class="space-y-5">
            @csrf
            @method('PATCH')
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ $member->user->name }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ $member->user->phone }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Monthly Fee (৳)</label>
                    <input type="number" name="monthly_fee_amount" value="{{ $member->monthly_fee_amount }}" required min="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Join Date</label>
                    <input type="date" name="join_date" value="{{ $member->join_date->format('Y-m-d') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'suspended' => 'Suspended'] as $v => $l)
                        <option value="{{ $v }}" {{ $member->status === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">{{ $member->notes }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm">Save Changes</button>
                <a href="{{ route('admin.members.show', $member) }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg font-medium hover:bg-gray-50 text-sm transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
