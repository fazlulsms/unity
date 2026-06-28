@extends('layouts.app')
@section('title', 'Edit Bank Withdrawal')
@section('page-title', 'Edit Bank Withdrawal')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        @include('admin.bank-accounts._errors')
        <form action="{{ route('admin.bank-withdrawals.update', $bankWithdrawal) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            @php $inp = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none'; @endphp
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Bank Account <span class="text-red-500">*</span></label>
                <select name="bank_account_id" required class="{{ $inp }} cursor-pointer">
                    @foreach($accounts as $a)
                    <option value="{{ $a->id }}" {{ old('bank_account_id', $bankWithdrawal->bank_account_id) == $a->id ? 'selected' : '' }}>{{ $a->bank_name }} — {{ $a->account_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Withdrawal Date <span class="text-red-500">*</span></label>
                    <input type="date" name="withdrawal_date" value="{{ old('withdrawal_date', $bankWithdrawal->withdrawal_date->format('Y-m-d')) }}" required class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Amount (৳) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" value="{{ old('amount', $bankWithdrawal->amount) }}" min="0.01" step="0.01" required class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Cheque Number</label>
                    <input type="text" name="cheque_number" value="{{ old('cheque_number', $bankWithdrawal->cheque_number) }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Purpose</label>
                    <input type="text" name="purpose" value="{{ old('purpose', $bankWithdrawal->purpose) }}" class="{{ $inp }}">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" rows="2" class="{{ $inp }}">{{ old('remarks', $bankWithdrawal->remarks) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Attachment</label>
                @if($bankWithdrawal->attachment_url)
                <p class="text-xs mb-1"><a href="{{ $bankWithdrawal->attachment_url }}" target="_blank" class="text-blue-600 hover:underline"><i class="fas fa-paperclip"></i> Current file</a> — upload to replace</p>
                @endif
                <input type="file" name="attachment" accept="image/jpeg,image/png,image/jpg,application/pdf"
                    class="{{ $inp }} file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm">Update Withdrawal</button>
                <a href="{{ route('admin.bank-withdrawals.index') }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
