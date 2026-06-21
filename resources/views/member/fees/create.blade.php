@extends('layouts.app')
@section('title', 'Submit Payment')
@section('page-title', 'Submit Monthly Payment')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Payment Submission</h2>
                <p class="text-xs text-gray-400 mt-0.5">Admin will verify and approve within 1-2 business days</p>
            </div>
            <a href="{{ route('member.fees.index') }}" class="btn-ghost btn-sm">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
        </div>
        <div class="card-body space-y-5">
            {{-- Info banner --}}
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3.5 text-sm text-blue-800">
                <i class="fas fa-circle-info text-blue-500 mt-0.5 shrink-0"></i>
                <span>Your monthly fee is <strong>৳{{ number_format($member->monthly_fee_amount, 0) }}</strong>.
                    Submit payment and upload proof for quick approval.</span>
            </div>

            @if($errors->any())
            <div class="alert-error">
                <i class="fas fa-circle-exclamation shrink-0 mt-0.5"></i>
                <div class="space-y-0.5">
                    @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
                </div>
            </div>
            @endif

            <form action="{{ route('member.fees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Month <span class="form-required">*</span></label>
                        <select name="month" required class="form-select">
                            @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0,0,0,$m,1)) }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Year <span class="form-required">*</span></label>
                        <select name="year" required class="form-select">
                            @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Amount (৳) <span class="form-required">*</span></label>
                        <input type="number" name="amount" required min="1" step="0.01"
                               value="{{ old('amount', $member->monthly_fee_amount) }}"
                               class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Payment Date <span class="form-required">*</span></label>
                        <input type="date" name="payment_date" required max="{{ date('Y-m-d') }}"
                               value="{{ old('payment_date', date('Y-m-d')) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Payment Method <span class="form-required">*</span></label>
                        <select name="payment_method" required class="form-select">
                            @foreach(['cash' => 'Cash', 'bank' => 'Bank Transfer', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket', 'other' => 'Other'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('payment_method') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Transaction Reference</label>
                        <input type="text" name="transaction_reference" placeholder="TXN ID / bKash number"
                               value="{{ old('transaction_reference') }}" class="form-input">
                        <p class="form-hint">Optional — helpful for mobile payments</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Payment Proof <span class="form-hint inline">(recommended)</span></label>
                        <input type="file" name="proof_attachment" accept="image/jpeg,image/png,image/jpg,application/pdf"
                               class="form-input file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="form-hint">Screenshot or PDF of payment — max 5MB</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2" placeholder="Any additional information…"
                                  class="form-textarea">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="btn-primary flex-1 justify-center btn-lg">
                        <i class="fas fa-paper-plane"></i> Submit Payment
                    </button>
                    <a href="{{ route('member.fees.index') }}" class="btn-secondary flex-1 justify-center btn-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
