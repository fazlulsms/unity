@extends('layouts.app')
@section('title', 'Add Manual Payment')
@section('page-title', 'Add Manual Payment')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-2xl space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())
    <div class="alert-error">
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm">Payment Details</p>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.collections.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Member select --}}
                <div>
                    <label class="form-label">Member <span class="form-required">*</span></label>
                    <select name="member_id" id="member_select" class="form-select" required>
                        <option value="">— Select member —</option>
                        @foreach($members as $m)
                        <option value="{{ $m->id }}"
                                data-fee="{{ $m->monthly_fee_amount }}"
                                {{ (old('member_id', $selected?->id) == $m->id) ? 'selected' : '' }}>
                            {{ $m->member_number }} — {{ $m->user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Period --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Month <span class="form-required">*</span></label>
                        <select name="month" class="form-select" required>
                            @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0,0,0,$m,1)) }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Year <span class="form-required">*</span></label>
                        <select name="year" class="form-select" required>
                            @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Expected & paid --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Expected Amount (৳)</label>
                        <input type="text" id="expected_display" class="form-input bg-gray-50 text-gray-500" readonly
                               placeholder="Select member first">
                    </div>
                    <div>
                        <label class="form-label">Amount Paid (৳) <span class="form-required">*</span></label>
                        <input type="number" name="amount" id="amount_input"
                               value="{{ old('amount') }}"
                               required min="0.01" step="0.01" class="form-input">
                    </div>
                </div>

                {{-- Payment date & method --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Payment Date <span class="form-required">*</span></label>
                        <input type="date" name="payment_date"
                               value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                               required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Payment Method <span class="form-required">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            @foreach(['cash','bank','bkash','nagad','rocket','other'] as $method)
                            <option value="{{ $method }}" {{ old('payment_method') === $method ? 'selected' : '' }}>
                                {{ ucfirst($method) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Reference --}}
                <div>
                    <label class="form-label">Transaction Reference (optional)</label>
                    <input type="text" name="transaction_reference"
                           value="{{ old('transaction_reference') }}"
                           placeholder="Bank ref, bKash txn ID, etc." class="form-input">
                </div>

                {{-- Notes --}}
                <div>
                    <label class="form-label">Notes (optional)</label>
                    <textarea name="notes" rows="2" class="form-textarea"
                              placeholder="Any additional notes…">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit" class="btn-success">
                        <i class="fas fa-check"></i> Record Payment &amp; Generate Receipt
                    </button>
                    <a href="{{ route('admin.collections.index') }}" class="btn btn-md btn-ghost text-gray-400">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <a href="{{ route('admin.collections.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to Collections
    </a>
</div>

<script>
document.getElementById('member_select').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const fee = opt.dataset.fee;
    const display = document.getElementById('expected_display');
    const input   = document.getElementById('amount_input');
    if (fee) {
        display.value = '৳ ' + parseFloat(fee).toLocaleString('en-BD', {minimumFractionDigits: 2});
        if (!input.value) input.value = parseFloat(fee).toFixed(2);
    } else {
        display.value = '';
    }
});
// Trigger on load if member is pre-selected
window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('member_select');
    if (sel.value) sel.dispatchEvent(new Event('change'));
});
</script>
@endsection
