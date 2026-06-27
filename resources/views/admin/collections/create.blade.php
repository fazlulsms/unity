@extends('layouts.app')
@section('title', 'Record Manual Payment')
@section('page-title', 'Collections')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="pb-20">

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="alert-error mb-4">
        <i class="fas fa-circle-exclamation shrink-0"></i>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- ── Page header ─────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-900 leading-tight">Record Manual Payment</h2>
            <p class="text-xs text-gray-400 mt-0.5">Payment is approved instantly — a receipt is auto-generated.</p>
        </div>
        <div class="flex items-center gap-2">
            <a id="btn-statement" href="#"
               class="btn btn-sm btn-secondary hidden" target="_blank">
                <i class="fas fa-file-alt"></i> Statement
            </a>
            <a id="btn-member" href="#"
               class="btn btn-sm btn-secondary hidden" target="_blank">
                <i class="fas fa-user"></i> View Member
            </a>
            <a href="{{ route('admin.collections.index') }}"
               class="btn btn-sm btn-ghost text-gray-400">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- ── Main grid ───────────────────────────────────────────── --}}
    <form id="payment-form"
          action="{{ route('admin.collections.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        <div class="grid lg:grid-cols-[1fr_310px] gap-5 items-start">

            {{-- ════════════════════════════════════════════════════
                 LEFT — Payment Form Card
                 ════════════════════════════════════════════════════ --}}
            <div class="card overflow-hidden">

                {{-- ❶ Member & Period ────────────────────────── --}}
                <div class="px-5 pt-5 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-md bg-blue-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-white" style="font-size:9px"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Member & Period</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="form-label">Member <span class="form-required">*</span></label>
                            <select name="member_id" id="member_select" class="form-select" required>
                                <option value="">— Select a member to begin —</option>
                                @foreach($members as $m)
                                <option value="{{ $m->id }}"
                                    {{ (old('member_id', $selected?->id) == $m->id) ? 'selected' : '' }}>
                                    {{ $m->member_number }} — {{ $m->user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Month <span class="form-required">*</span></label>
                                <select name="month" id="month_select" class="form-select" required>
                                    @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('month', now()->month) == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0,0,0,$i,1)) }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Year <span class="form-required">*</span></label>
                                <select name="year" id="year_select" class="form-select" required>
                                    @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Duplicate warning --}}
                        <div id="dup-warn" class="hidden flex items-start gap-2.5 bg-amber-50 border border-amber-200 rounded-lg px-3.5 py-2.5">
                            <i class="fas fa-triangle-exclamation text-amber-500 text-sm mt-0.5 shrink-0"></i>
                            <p class="text-xs font-medium text-amber-800 leading-relaxed">
                                This month is already <strong>paid</strong>. Recording again will create a duplicate entry.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ❷ Amount ──────────────────────────────────── --}}
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/40">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-md bg-emerald-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-coins text-white" style="font-size:9px"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Payment Amount</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Expected --}}
                        <div>
                            <label class="text-xs font-medium text-gray-400 mb-1.5 block">Expected (Monthly Fee)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 font-bold text-xl leading-none select-none">৳</span>
                                <input id="expected_display" type="text" readonly
                                       class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-3.5 text-2xl font-bold text-gray-300 bg-gray-50 cursor-not-allowed tracking-tight"
                                       placeholder="—">
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5">Member's monthly fee rate</p>
                        </div>
                        {{-- Paid --}}
                        <div>
                            <label class="text-xs font-medium text-gray-700 mb-1.5 block">Amount Paid <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-500 font-bold text-xl leading-none select-none">৳</span>
                                <input id="amount_input" type="number" name="amount"
                                       value="{{ old('amount') }}"
                                       required min="0.01" step="0.01"
                                       class="w-full border-2 border-emerald-300 rounded-xl pl-9 pr-4 py-3.5 text-2xl font-bold text-emerald-700 bg-white focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition tracking-tight"
                                       placeholder="0.00">
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5">Actual amount received</p>
                        </div>
                    </div>

                    {{-- Difference indicator --}}
                    <div id="diff-row" class="hidden mt-3 flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-all"></div>
                </div>

                {{-- ❸ Payment Details ─────────────────────────── --}}
                <div class="px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 rounded-md bg-amber-500 flex items-center justify-center shrink-0">
                            <i class="fas fa-credit-card text-white" style="font-size:9px"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Payment Details</span>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="form-label">Date <span class="form-required">*</span></label>
                            <input type="date" name="payment_date"
                                   value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                                   required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Method <span class="form-required">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                @foreach(['cash'=>'Cash','bank'=>'Bank Transfer','bkash'=>'bKash','nagad'=>'Nagad','rocket'=>'Rocket','other'=>'Other'] as $v=>$l)
                                <option value="{{ $v }}" {{ old('payment_method') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Reference</label>
                            <input type="text" name="transaction_reference"
                                   value="{{ old('transaction_reference') }}"
                                   placeholder="Txn ID / ref…" class="form-input">
                        </div>
                    </div>
                </div>

                {{-- ❹ Proof & Notes ───────────────────────────── --}}
                <div class="px-5 py-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">
                                Payment Proof
                                <span class="font-normal text-gray-400">(optional)</span>
                            </label>
                            <label id="proof-zone"
                                   class="flex items-center gap-3 border-2 border-dashed border-gray-200 rounded-xl px-4 py-3 cursor-pointer hover:border-violet-300 hover:bg-violet-50/40 transition-colors group">
                                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center shrink-0 group-hover:bg-violet-200 transition-colors">
                                    <i class="fas fa-paperclip text-violet-500 text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p id="proof-name" class="text-sm text-gray-500 truncate">Click to attach</p>
                                    <p class="text-xs text-gray-400">JPG, PNG or PDF · max 5 MB</p>
                                </div>
                                <input id="proof-input" type="file" name="proof_attachment"
                                       accept="image/jpeg,image/png,image/jpg,application/pdf"
                                       class="hidden">
                            </label>
                        </div>
                        <div>
                            <label class="form-label">
                                Notes
                                <span class="font-normal text-gray-400">(optional)</span>
                            </label>
                            <textarea name="notes" rows="3" class="form-textarea"
                                      style="resize:none"
                                      placeholder="Additional notes…">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>{{-- end left card --}}

            {{-- ════════════════════════════════════════════════════
                 RIGHT — Member Summary Panel (sticky)
                 ════════════════════════════════════════════════════ --}}
            <div class="sticky top-6 space-y-3">

                {{-- Placeholder: no member selected --}}
                <div id="rp-placeholder" class="card">
                    <div class="p-7 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center mx-auto mb-3 shadow-inner">
                            <i class="fas fa-user-circle text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-300">No member selected</p>
                        <p class="text-xs text-gray-300 mt-1">Select a member to see their summary.</p>
                    </div>
                </div>

                {{-- ── Member mini-profile ─────────────────────── --}}
                <div id="rp-member" class="card hidden">
                    <div class="p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <img id="rp-photo" src="" alt=""
                                 class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5 flex-wrap mb-0.5">
                                    <p id="rp-name" class="font-bold text-gray-900 text-sm leading-tight truncate"></p>
                                    <span id="rp-badge" class="badge-active text-xs"></span>
                                </div>
                                <p id="rp-num" class="text-xs font-mono text-gray-400"></p>
                            </div>
                        </div>
                        <div class="space-y-1 text-xs">
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-phone w-3.5 text-gray-300 shrink-0"></i>
                                <span id="rp-phone"></span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fas fa-envelope w-3.5 text-gray-300 shrink-0"></i>
                                <span id="rp-email" class="truncate"></span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-400">
                                <i class="fas fa-calendar-days w-3.5 text-gray-300 shrink-0"></i>
                                <span id="rp-since"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Financial stat grid (2×3) ───────────────── --}}
                <div id="rp-stats" class="hidden">
                    <div class="grid grid-cols-2 gap-2">

                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-3">
                            <p class="text-xs font-semibold text-blue-500 mb-1">Monthly Fee</p>
                            <p id="st-fee" class="text-base font-bold text-blue-700 leading-tight">—</p>
                        </div>

                        <div class="bg-orange-50 border border-orange-100 rounded-xl p-3">
                            <p class="text-xs font-semibold text-orange-500 mb-1">Total Payable</p>
                            <p id="st-payable" class="text-base font-bold text-orange-600 leading-tight">—</p>
                        </div>

                        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3">
                            <p class="text-xs font-semibold text-emerald-600 mb-1">Total Paid</p>
                            <p id="st-paid" class="text-base font-bold text-emerald-700 leading-tight">—</p>
                        </div>

                        <div id="st-due-box" class="bg-red-50 border border-red-100 rounded-xl p-3">
                            <p id="st-due-lbl" class="text-xs font-semibold text-red-500 mb-1">Outstanding</p>
                            <p id="st-due" class="text-base font-bold text-red-600 leading-tight">—</p>
                        </div>

                        <div class="bg-violet-50 border border-violet-100 rounded-xl p-3">
                            <p class="text-xs font-semibold text-violet-500 mb-1">This Month</p>
                            <p id="st-thismonth" class="text-sm font-bold leading-tight">—</p>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-3">
                            <p class="text-xs font-semibold text-gray-400 mb-1">Last Payment</p>
                            <p id="st-lastpay" class="text-xs font-semibold text-gray-600 leading-snug">—</p>
                        </div>

                    </div>
                </div>

                {{-- ── Recent Payments ──────────────────────────── --}}
                <div id="rp-recent" class="card hidden overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-gray-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recent Payments</span>
                        <span class="text-xs text-gray-300">last 5</span>
                    </div>
                    <div id="rp-recent-list" class="divide-y divide-gray-50/80"></div>
                </div>

            </div>{{-- end right panel --}}

        </div>
    </form>

</div>{{-- end pb-20 wrapper --}}

{{-- ── Fixed bottom action bar ─────────────────────────────────── --}}
<div class="fixed bottom-0 left-0 lg:left-64 right-0 z-20
            bg-white/95 backdrop-blur-sm border-t border-gray-200 shadow-lg px-6 py-3">
    <div class="flex items-center gap-3">
        <button form="payment-form" type="submit"
                class="btn-success"
                onclick="return confirm('Record this payment and generate a receipt?')">
            <i class="fas fa-check-circle"></i> Save &amp; Generate Receipt
        </button>
        <span id="bar-summary" class="text-sm text-gray-400 hidden sm:block">
            Select a member and complete the form above.
        </span>
        <div class="ml-auto">
            <a href="{{ route('admin.collections.index') }}"
               class="btn btn-md btn-ghost text-gray-400">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </div>
</div>

{{-- ── Embedded member data for JS ─────────────────────────────── --}}
<script>
const MDATA     = @json($memberData);
const NOW_MONTH = {{ now()->month }};
const NOW_YEAR  = {{ now()->year }};

// ── Helpers ──────────────────────────────────────────────────────

function money(n) {
    n = parseFloat(n);
    return '৳ ' + (isNaN(n) ? '—' : n.toLocaleString('en-BD', {minimumFractionDigits: 0, maximumFractionDigits: 0}));
}

function el(id) { return document.getElementById(id); }
function show(id) { el(id).classList.remove('hidden'); }
function hide(id) { el(id).classList.add('hidden'); }

// ── Duplicate detection ───────────────────────────────────────────

function checkDuplicate() {
    const memberId = el('member_select').value;
    const m        = parseInt(el('month_select').value, 10);
    const y        = el('year_select').value;
    const key      = y + '-' + String(m).padStart(2, '0');
    const hasDup   = memberId && MDATA[memberId] && (MDATA[memberId].paid_months || []).includes(key);
    el('dup-warn').classList.toggle('hidden', !hasDup);
}

// ── Difference indicator ──────────────────────────────────────────

function updateDiff() {
    const expected = parseFloat(el('expected_display').value) || 0;
    const paid     = parseFloat(el('amount_input').value)     || 0;
    const row      = el('diff-row');

    if (!expected || !paid) { row.classList.add('hidden'); updateBar(); return; }

    const diff = paid - expected;
    row.classList.remove('hidden');

    if (Math.abs(diff) < 0.01) {
        row.className = 'mt-3 flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-all bg-emerald-50 text-emerald-700 border border-emerald-100';
        row.innerHTML = '<i class="fas fa-check-circle text-emerald-500"></i> Full payment — matches the monthly fee exactly.';
    } else if (diff < 0) {
        row.className = 'mt-3 flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-all bg-amber-50 text-amber-700 border border-amber-100';
        row.innerHTML = '<i class="fas fa-minus-circle text-amber-500"></i> Partial payment — <strong>' + money(Math.abs(diff)) + '</strong>&nbsp;still outstanding.';
    } else {
        row.className = 'mt-3 flex items-center gap-2 px-3.5 py-2 rounded-lg text-sm font-medium transition-all bg-blue-50 text-blue-700 border border-blue-100';
        row.innerHTML = '<i class="fas fa-plus-circle text-blue-500"></i> Excess — <strong>' + money(diff) + '</strong>&nbsp;over the monthly fee.';
    }

    updateBar();
}

// ── Bottom bar summary ────────────────────────────────────────────

function updateBar() {
    const memberId = el('member_select').value;
    const m        = MDATA[memberId];
    const paid     = parseFloat(el('amount_input').value) || 0;
    const monthOpt = el('month_select').options[el('month_select').selectedIndex];
    const year     = el('year_select').value;
    const bar      = el('bar-summary');

    if (m && paid > 0) {
        bar.textContent = 'Recording ' + money(paid) + ' for ' + m.name + ' — ' + (monthOpt?.text ?? '') + ' ' + year;
        bar.className = 'text-sm text-gray-600 font-medium hidden sm:block';
    } else if (m) {
        bar.textContent = 'Recording payment for ' + m.name + ' — enter amount above.';
        bar.className = 'text-sm text-gray-400 hidden sm:block';
    } else {
        bar.textContent = 'Select a member and complete the form above.';
        bar.className = 'text-sm text-gray-400 hidden sm:block';
    }
}

// ── Member selection handler ──────────────────────────────────────

function onMemberChange() {
    const memberId = el('member_select').value;
    const m        = memberId ? MDATA[memberId] : null;

    if (!m) {
        show('rp-placeholder');
        hide('rp-member'); hide('rp-stats'); hide('rp-recent');
        hide('btn-statement'); hide('btn-member');
        el('expected_display').value = '';
        el('diff-row').classList.add('hidden');
        updateBar();
        return;
    }

    // Show all panels
    hide('rp-placeholder');
    show('rp-member'); show('rp-stats'); show('rp-recent');

    // Header action buttons
    el('btn-statement').href = m.statement_url; show('btn-statement');
    el('btn-member').href    = m.profile_url;   show('btn-member');

    // ── Mini profile ────────────────────────────────────────────
    el('rp-photo').src            = m.photo;
    el('rp-name').textContent     = m.name;
    el('rp-num').textContent      = m.number;
    el('rp-phone').textContent    = m.phone  || '—';
    el('rp-email').textContent    = m.email  || '—';
    el('rp-since').textContent    = 'Member since ' + m.join_date;

    const badge = el('rp-badge');
    badge.className   = m.status === 'active' ? 'badge-active text-xs' : 'badge-inactive text-xs';
    badge.textContent = m.status.charAt(0).toUpperCase() + m.status.slice(1);

    // ── Stat cards ──────────────────────────────────────────────
    el('st-fee').textContent     = money(m.monthly_fee);
    el('st-payable').textContent = money(m.total_payable);
    el('st-paid').textContent    = money(m.total_paid);

    if (m.total_due > 0) {
        el('st-due-box').className = 'bg-red-50 border border-red-100 rounded-xl p-3';
        el('st-due-lbl').className = 'text-xs font-semibold text-red-500 mb-1';
        el('st-due').className     = 'text-base font-bold text-red-600 leading-tight';
        el('st-due').textContent   = money(m.total_due);
    } else {
        el('st-due-box').className = 'bg-emerald-50 border border-emerald-100 rounded-xl p-3';
        el('st-due-lbl').className = 'text-xs font-semibold text-emerald-600 mb-1';
        el('st-due').className     = 'text-base font-bold text-emerald-600 leading-tight';
        el('st-due').textContent   = '৳ 0 ✓';
    }

    // This month status
    const thisKey  = NOW_YEAR + '-' + String(NOW_MONTH).padStart(2, '0');
    const thisPaid = (m.paid_months || []).includes(thisKey);
    el('st-thismonth').textContent = thisPaid ? '✓ Paid' : 'Not yet';
    el('st-thismonth').className   = thisPaid
        ? 'text-sm font-bold text-emerald-600 leading-tight'
        : 'text-sm font-bold text-amber-500 leading-tight';

    // Last payment
    const last = m.payments && m.payments.length > 0 ? m.payments[0] : null;
    el('st-lastpay').innerHTML = last
        ? last.month_name + ' ' + last.year + '<br><span class="font-bold text-gray-700">' + money(last.amount) + '</span>'
        : '<span class="text-gray-400 font-normal">No history</span>';

    // ── Expected amount ─────────────────────────────────────────
    el('expected_display').value = parseFloat(m.monthly_fee).toFixed(0);
    if (!el('amount_input').value) {
        el('amount_input').value = parseFloat(m.monthly_fee).toFixed(2);
    }

    // ── Recent payments list ────────────────────────────────────
    const list = el('rp-recent-list');
    if (m.payments && m.payments.length > 0) {
        list.innerHTML = m.payments.map((p, i) => `
            <div class="flex items-center justify-between px-4 py-2.5 hover:bg-gray-50/80 transition-colors">
                <div>
                    <p class="text-xs font-semibold text-gray-700">${p.month_name} ${p.year}</p>
                    <p class="text-xs text-gray-400">${p.date} · ${p.method}</p>
                </div>
                <span class="text-sm font-bold text-emerald-600">${money(p.amount)}</span>
            </div>
        `).join('');
    } else {
        list.innerHTML = '<p class="px-4 py-3 text-xs text-gray-400">No payment history found.</p>';
    }

    checkDuplicate();
    updateDiff();
}

// ── Event listeners ───────────────────────────────────────────────

el('member_select').addEventListener('change', onMemberChange);
el('month_select').addEventListener('change',  () => { checkDuplicate(); updateBar(); });
el('year_select').addEventListener('change',   () => { checkDuplicate(); updateBar(); });
el('amount_input').addEventListener('input',   updateDiff);
el('proof-input').addEventListener('change', function () {
    el('proof-name').textContent = this.files.length ? this.files[0].name : 'Click to attach';
});

// ── Init on page load (handles ?member= pre-selection) ────────────
window.addEventListener('DOMContentLoaded', () => {
    if (el('member_select').value) onMemberChange();
});
</script>
@endsection
