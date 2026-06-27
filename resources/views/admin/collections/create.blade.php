@extends('layouts.app')
@section('title', 'Add Manual Payment')
@section('page-title', 'Add Manual Payment')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())
    <div class="alert-error">
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Page header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center shadow-sm">
                <i class="fas fa-money-bill-wave text-white text-sm"></i>
            </div>
            <div>
                <p class="font-bold text-gray-900 text-lg leading-tight">Record Manual Payment</p>
                <p class="text-xs text-gray-400">Admin-entered payment with instant receipt generation</p>
            </div>
        </div>
        <a href="{{ route('admin.collections.index') }}" class="btn btn-md btn-ghost text-gray-400">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.collections.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid lg:grid-cols-3 gap-5">

            {{-- ── LEFT COLUMN (2/3) ─────────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- SECTION 1: Member & Period ──────────────────────── --}}
                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/60 to-white overflow-hidden shadow-sm">
                    <div class="px-5 py-3 border-b border-indigo-100 flex items-center gap-2 bg-indigo-50/80">
                        <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center">
                            <i class="fas fa-user text-white text-xs"></i>
                        </div>
                        <p class="font-semibold text-indigo-900 text-sm">Member & Period</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="form-label">Member <span class="form-required">*</span></label>
                            <select name="member_id" id="member_select" class="form-select" required>
                                <option value="">— Select member —</option>
                                @foreach($members as $m)
                                <option value="{{ $m->id }}"
                                        data-fee="{{ $m->monthly_fee_amount }}"
                                        data-name="{{ $m->user->name }}"
                                        data-number="{{ $m->member_number }}"
                                        data-due="{{ $m->total_due }}"
                                        {{ (old('member_id', $selected?->id) == $m->id) ? 'selected' : '' }}>
                                    {{ $m->member_number }} — {{ $m->user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

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
                    </div>
                </div>

                {{-- SECTION 2: Amount ───────────────────────────────── --}}
                <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50/60 to-white overflow-hidden shadow-sm">
                    <div class="px-5 py-3 border-b border-emerald-100 flex items-center gap-2 bg-emerald-50/80">
                        <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center">
                            <i class="fas fa-coins text-white text-xs"></i>
                        </div>
                        <p class="font-semibold text-emerald-900 text-sm">Payment Amount</p>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Expected Amount (৳)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">৳</span>
                                    <input type="text" id="expected_display"
                                           class="form-input pl-7 bg-gray-50 text-gray-500 cursor-not-allowed" readonly
                                           placeholder="Auto-filled on member select">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Member's monthly fee rate</p>
                            </div>
                            <div>
                                <label class="form-label">Amount Paid (৳) <span class="form-required">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-600 text-sm font-bold">৳</span>
                                    <input type="number" name="amount" id="amount_input"
                                           value="{{ old('amount') }}"
                                           required min="0.01" step="0.01"
                                           class="form-input pl-7 border-emerald-200 focus:ring-emerald-400 font-semibold text-emerald-800">
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Actual amount received</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: Payment Details ──────────────────────── --}}
                <div class="rounded-2xl border border-amber-100 bg-gradient-to-br from-amber-50/60 to-white overflow-hidden shadow-sm">
                    <div class="px-5 py-3 border-b border-amber-100 flex items-center gap-2 bg-amber-50/80">
                        <div class="w-7 h-7 rounded-lg bg-amber-500 flex items-center justify-center">
                            <i class="fas fa-credit-card text-white text-xs"></i>
                        </div>
                        <p class="font-semibold text-amber-900 text-sm">Payment Details</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Payment Date <span class="form-required">*</span></label>
                                <input type="date" name="payment_date"
                                       value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                       required class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Payment Method <span class="form-required">*</span></label>
                                <select name="payment_method" id="method_select" class="form-select" required>
                                    @foreach(['cash' => 'Cash', 'bank' => 'Bank Transfer', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket', 'other' => 'Other'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('payment_method') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Transaction Reference <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="text" name="transaction_reference"
                                   value="{{ old('transaction_reference') }}"
                                   placeholder="Bank ref, bKash txn ID, etc."
                                   class="form-input">
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── RIGHT COLUMN (1/3) ────────────────────────────────── --}}
            <div class="space-y-4">

                {{-- Live member summary card --}}
                <div id="member_summary" class="rounded-2xl border border-gray-100 bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg overflow-hidden" style="display:none">
                    <div class="p-5">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Selected Member</p>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                                <i class="fas fa-user text-slate-300"></i>
                            </div>
                            <div>
                                <p id="summary_name" class="font-bold text-white text-sm leading-tight"></p>
                                <p id="summary_number" class="text-xs text-slate-400 font-mono"></p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center py-2 border-b border-white/10">
                                <span class="text-xs text-slate-400">Monthly Fee</span>
                                <span id="summary_fee" class="text-sm font-bold text-white"></span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-xs text-slate-400">Outstanding Due</span>
                                <span id="summary_due" class="text-sm font-bold text-red-400"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Placeholder when no member selected --}}
                <div id="member_placeholder" class="rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-6 text-center">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user-circle text-gray-300 text-xl"></i>
                    </div>
                    <p class="text-sm text-gray-400 font-medium">Select a member</p>
                    <p class="text-xs text-gray-300 mt-0.5">to see their details here</p>
                </div>

                {{-- Proof & Notes ──────────────────────────────────── --}}
                <div class="rounded-2xl border border-violet-100 bg-gradient-to-br from-violet-50/60 to-white overflow-hidden shadow-sm">
                    <div class="px-5 py-3 border-b border-violet-100 flex items-center gap-2 bg-violet-50/80">
                        <div class="w-7 h-7 rounded-lg bg-violet-600 flex items-center justify-center">
                            <i class="fas fa-paperclip text-white text-xs"></i>
                        </div>
                        <p class="font-semibold text-violet-900 text-sm">Proof & Notes</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="form-label">Payment Proof <span class="text-gray-400 font-normal">(optional)</span></label>
                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-violet-200 rounded-xl cursor-pointer hover:bg-violet-50/50 transition-colors" id="proof_label">
                                <i class="fas fa-cloud-upload-alt text-violet-400 text-xl mb-1"></i>
                                <span class="text-xs text-violet-500 font-medium" id="proof_text">Click to upload</span>
                                <span class="text-xs text-gray-400">JPG, PNG or PDF, max 5MB</span>
                                <input type="file" name="proof_attachment" id="proof_input"
                                       accept="image/jpeg,image/png,image/jpg,application/pdf"
                                       class="hidden" onchange="updateProofLabel(this)">
                            </label>
                        </div>
                        <div>
                            <label class="form-label">Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                            <textarea name="notes" rows="3" class="form-textarea"
                                      placeholder="Any additional notes…">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Submit ──────────────────────────────────────────── --}}
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5 space-y-3">
                    <div class="flex items-start gap-2 text-xs text-emerald-700">
                        <i class="fas fa-info-circle mt-0.5 shrink-0"></i>
                        <span>Payment is recorded as <strong>approved</strong> immediately and a receipt is auto-generated.</span>
                    </div>
                    <button type="submit" class="btn-success w-full text-sm py-3"
                            onclick="return confirm('Record this payment and generate a receipt?')">
                        <i class="fas fa-check-circle mr-1"></i> Record Payment & Generate Receipt
                    </button>
                    <a href="{{ route('admin.collections.index') }}"
                       class="block text-center text-sm text-gray-400 hover:text-gray-600 py-1">
                        Cancel
                    </a>
                </div>

            </div>
        </div>
    </form>

</div>

<script>
function updateProofLabel(input) {
    const label = document.getElementById('proof_text');
    label.textContent = input.files.length ? input.files[0].name : 'Click to upload';
}

const memberSelect = document.getElementById('member_select');
const summary      = document.getElementById('member_summary');
const placeholder  = document.getElementById('member_placeholder');

memberSelect.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const fee = opt.dataset.fee;

    const display = document.getElementById('expected_display');
    const input   = document.getElementById('amount_input');

    if (fee) {
        const feeNum = parseFloat(fee);
        display.value = feeNum.toLocaleString('en-BD', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        if (!input.value) input.value = feeNum.toFixed(2);

        document.getElementById('summary_name').textContent   = opt.dataset.name   ?? '—';
        document.getElementById('summary_number').textContent = opt.dataset.number  ?? '';
        document.getElementById('summary_fee').textContent    = '৳ ' + feeNum.toLocaleString('en-BD', {minimumFractionDigits: 2});
        const due = parseFloat(opt.dataset.due ?? 0);
        document.getElementById('summary_due').textContent    = due > 0
            ? '৳ ' + due.toLocaleString('en-BD', {minimumFractionDigits: 2})
            : 'No due';
        document.getElementById('summary_due').className = due > 0
            ? 'text-sm font-bold text-red-400'
            : 'text-sm font-bold text-emerald-400';

        summary.style.display     = '';
        placeholder.style.display = 'none';
    } else {
        display.value = '';
        summary.style.display     = 'none';
        placeholder.style.display = '';
    }
});

window.addEventListener('DOMContentLoaded', () => {
    if (memberSelect.value) memberSelect.dispatchEvent(new Event('change'));
});
</script>
@endsection
