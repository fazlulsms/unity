@extends('layouts.app')
@section('title', 'Add Bank Deposit')
@section('page-title', 'Record Bank Deposit')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        @include('admin.bank-accounts._errors')
        @if($accounts->isEmpty())
        <div class="alert-warning mb-4">
            <i class="fas fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0"></i>
            <span>No active bank accounts. <a href="{{ route('admin.bank-accounts.create') }}" class="underline font-semibold">Add one first</a>.</span>
        </div>
        @endif
        <form action="{{ route('admin.bank-deposits.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @php $inp = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none'; @endphp
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Bank Account <span class="text-red-500">*</span></label>
                <select name="bank_account_id" required class="{{ $inp }} cursor-pointer">
                    <option value="">Select account…</option>
                    @foreach($accounts as $a)
                    <option value="{{ $a->id }}" {{ old('bank_account_id', $selected) == $a->id ? 'selected' : '' }}>{{ $a->bank_name }} — {{ $a->account_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Deposit Date <span class="text-red-500">*</span></label>
                    <input type="date" name="deposit_date" value="{{ old('deposit_date', date('Y-m-d')) }}" required class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Amount Deposited (৳) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount') }}" min="0.01" step="0.01" required class="{{ $inp }}">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Source / Collection Reference</label>
                <input type="text" name="source_reference" value="{{ old('source_reference') }}" placeholder="e.g. June 2026 cash collection" class="{{ $inp }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" rows="2" class="{{ $inp }}">{{ old('remarks') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Deposit Slip / Attachment</label>
                <input type="file" name="attachment" accept="image/jpeg,image/png,image/jpg,application/pdf"
                    class="{{ $inp }} file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                <p class="mt-1 text-xs text-gray-400">JPG, PNG or PDF, max 5MB</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm">Save Deposit</button>
                <a href="{{ route('admin.bank-deposits.index') }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
