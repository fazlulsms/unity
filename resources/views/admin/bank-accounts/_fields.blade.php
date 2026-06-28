@php $inp = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none'; @endphp
<div class="grid sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Bank Name <span class="text-red-500">*</span></label>
        <input type="text" name="bank_name" value="{{ old('bank_name', $account->bank_name ?? '') }}" required class="{{ $inp }}">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Branch Name</label>
        <input type="text" name="branch_name" value="{{ old('branch_name', $account->branch_name ?? '') }}" class="{{ $inp }}">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Account Name <span class="text-red-500">*</span></label>
        <input type="text" name="account_name" value="{{ old('account_name', $account->account_name ?? '') }}" required class="{{ $inp }}">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Account Number <span class="text-red-500">*</span></label>
        <input type="text" name="account_number" value="{{ old('account_number', $account->account_number ?? '') }}" required class="{{ $inp }}">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Account Type <span class="text-red-500">*</span></label>
        <select name="account_type" class="{{ $inp }} cursor-pointer">
            @foreach(['savings' => 'Savings', 'current' => 'Current', 'fixed' => 'Fixed Deposit', 'other' => 'Other'] as $val => $label)
            <option value="{{ $val }}" {{ old('account_type', $account->account_type ?? 'savings') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Opening Balance (৳) <span class="text-red-500">*</span></label>
        <input type="number" name="opening_balance" value="{{ old('opening_balance', $account->opening_balance ?? 0) }}" min="0" step="0.01" required class="{{ $inp }}">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select name="status" class="{{ $inp }} cursor-pointer">
            @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $val => $label)
            <option value="{{ $val }}" {{ old('status', $account->status ?? 'active') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
        <textarea name="notes" rows="3" class="{{ $inp }}">{{ old('notes', $account->notes ?? '') }}</textarea>
    </div>
</div>
