@extends('layouts.app')
@section('title', 'Close FDR — ' . $fdr->fdr_number)
@section('page-title', 'Close / Mature FDR')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-lg space-y-5">

    {{-- FDR summary card --}}
    <div class="card">
        <div class="card-body">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs text-gray-400 font-medium">FDR #{{ $fdr->fdr_number }}</p>
                    <p class="text-lg font-bold text-gray-900 mt-0.5">{{ $fdr->bank_name }}{{ $fdr->branch ? ' — ' . $fdr->branch : '' }}</p>
                    <p class="text-sm text-gray-600 mt-1">Principal: <strong>৳ {{ number_format($fdr->principal_amount, 2) }}</strong> · Rate: <strong>{{ $fdr->interest_rate }}%</strong></p>
                    <p class="text-xs text-gray-400 mt-0.5">Opened: {{ $fdr->opening_date->format('d M Y') }} · Maturity: {{ $fdr->maturity_date->format('d M Y') }}</p>
                </div>
                <span class="badge-active shrink-0">Active</span>
            </div>
        </div>
    </div>

    {{-- Notice --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-800">
        <i class="fas fa-info-circle mr-1.5"></i>
        The interest amount entered here will be automatically posted to <strong>Other Income</strong> as an FDR Interest entry. Only interest goes to income — the principal stays in FDR records.
    </div>

    @if($errors->any())
    <div class="alert-error">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-header"><p class="font-semibold text-gray-800 text-sm">Closure Details</p></div>
        <div class="card-body">
            <form action="{{ route('admin.fdr.close.store', $fdr) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Closure / Maturity Date <span class="form-required">*</span></label>
                        <input type="date" name="closure_date" value="{{ old('closure_date', now()->format('Y-m-d')) }}"
                               required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Status <span class="form-required">*</span></label>
                        <select name="status" required class="form-input">
                            <option value="matured"  {{ old('status') === 'matured'  ? 'selected' : '' }}>Matured</option>
                            <option value="closed"   {{ old('status') === 'closed'   ? 'selected' : '' }}>Closed Early</option>
                            <option value="renewed"  {{ old('status') === 'renewed'  ? 'selected' : '' }}>Renewed</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Principal Returned (৳)</label>
                        <input type="number" name="principal_returned" value="{{ old('principal_returned', $fdr->principal_amount) }}"
                               min="0" step="0.01" class="form-input" placeholder="{{ number_format($fdr->principal_amount, 2) }}">
                        <p class="text-xs text-gray-400 mt-1">Defaults to the original principal.</p>
                    </div>
                    <div>
                        <label class="form-label">Actual Interest Received (৳) <span class="form-required">*</span></label>
                        <input type="number" name="interest_received" value="{{ old('interest_received') }}"
                               required min="0" step="0.01" class="form-input"
                               placeholder="0.00">
                        <p class="text-xs text-gray-400 mt-1">Gross interest — posted to Other Income.</p>
                    </div>
                    <div>
                        <label class="form-label">Tax / Deduction (৳)</label>
                        <input type="number" name="tax_deduction" value="{{ old('tax_deduction', 0) }}"
                               min="0" step="0.01" class="form-input" placeholder="0.00">
                        <p class="text-xs text-gray-400 mt-1">Tax withheld on interest, if any.</p>
                    </div>
                    <div>
                        <label class="form-label">Actual Maturity Amount (৳)</label>
                        <input type="number" name="actual_maturity_amount" value="{{ old('actual_maturity_amount') }}"
                               min="0" step="0.01" class="form-input"
                               placeholder="{{ $fdr->expected_maturity_amount ? number_format($fdr->expected_maturity_amount, 2) : '0.00' }}">
                        <p class="text-xs text-gray-400 mt-1">Principal + interest received from bank.</p>
                    </div>
                </div>
                @if($fdr->bank_account_id)
                <div class="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 text-sm text-blue-800">
                    <i class="fas fa-building-columns mr-1.5"></i>
                    On closure, <strong>{{ $fdr->bankAccount->bank_name ?? 'the linked account' }}</strong>'s available balance increases by Principal Returned + Net Interest (interest − tax).
                </div>
                @endif

                <div>
                    <label class="form-label">Closure Proof / Attachment</label>
                    <input type="file" name="closure_attachment" accept="image/jpeg,image/png,application/pdf"
                           class="form-input">
                </div>

                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-textarea"
                              placeholder="Any additional notes…">{{ old('notes', $fdr->notes) }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="btn btn-sm btn-success flex-1"
                            onclick="return confirm('Close this FDR and post interest to Other Income?')">
                        <i class="fas fa-check-circle"></i> Confirm Closure
                    </button>
                    <a href="{{ route('admin.fdr.show', $fdr) }}" class="btn btn-sm btn-secondary flex-1 text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
