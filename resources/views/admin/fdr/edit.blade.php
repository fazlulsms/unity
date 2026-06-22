@extends('layouts.app')
@section('title', 'Edit FDR')
@section('page-title', 'Edit FDR Record')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.fdr.update', $fdr) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $fdr->bank_name) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Branch</label>
                    <input type="text" name="branch" value="{{ old('branch', $fdr->branch) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">FDR Number</label>
                    <input type="text" name="fdr_number" value="{{ old('fdr_number', $fdr->fdr_number) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach(['active', 'matured', 'renewed', 'closed'] as $s)
                        <option value="{{ $s }}" {{ old('status', $fdr->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Opening Date</label>
                    <input type="date" name="opening_date" value="{{ old('opening_date', $fdr->opening_date->format('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Maturity Date</label>
                    <input type="date" name="maturity_date" value="{{ old('maturity_date', $fdr->maturity_date->format('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Principal Amount (৳)</label>
                    <input type="number" name="principal_amount" value="{{ old('principal_amount', $fdr->principal_amount) }}" required min="1" step="0.01"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Interest Rate (%)</label>
                    <input type="number" name="interest_rate" value="{{ old('interest_rate', $fdr->interest_rate) }}" required min="0" max="100" step="0.01"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Expected Maturity</label>
                    <input type="number" name="expected_maturity_amount" value="{{ old('expected_maturity_amount', $fdr->expected_maturity_amount) }}" step="0.01"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Interest Received</label>
                    <input type="number" name="interest_received" value="{{ old('interest_received', $fdr->interest_received) }}" min="0" step="0.01"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_public_reference" value="1" {{ $fdr->is_public_reference ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300">
                        <span class="text-xs font-medium text-gray-700">Show FDR number/reference publicly</span>
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">{{ old('notes', $fdr->notes) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Document / Attachment</label>
                    @if($fdr->attachment_url)
                    <div class="mb-2 flex items-center gap-3 text-xs text-gray-600">
                        <i class="fas fa-paperclip text-gray-400"></i>
                        <a href="{{ $fdr->attachment_url }}" target="_blank" class="text-blue-600 hover:underline">View current attachment</a>
                        <span class="text-gray-400">(upload a new file to replace)</span>
                    </div>
                    @endif
                    <input type="file" name="attachment" accept="image/jpeg,image/png,image/jpg,application/pdf"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="mt-1 text-xs text-gray-400">FDR certificate, bank letter — JPG, PNG or PDF, max 5MB</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm">Update FDR</button>
                <a href="{{ route('admin.fdr.index') }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
