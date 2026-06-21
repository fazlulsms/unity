@extends('layouts.app')
@section('title', 'Add Expense')
@section('page-title', 'Add Expense')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Expense Details</h2>
                <p class="text-xs text-gray-400 mt-0.5">All fields marked <span class="text-red-500">*</span> are required</p>
            </div>
            <a href="{{ route('admin.expenses.index') }}" class="btn-ghost btn-sm">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @if($errors->any())
                <div class="alert-error">
                    <i class="fas fa-circle-exclamation shrink-0 mt-0.5"></i>
                    <div>
                        @foreach($errors->all() as $e)
                        <p>{{ $e }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Date <span class="form-required">*</span></label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Category <span class="form-required">*</span></label>
                        <input type="text" name="category" value="{{ old('category') }}" required
                               list="categories" placeholder="e.g. Food & Refreshment" class="form-input">
                        <datalist id="categories">
                            @foreach($categories ?? [] as $cat)<option value="{{ $cat }}">@endforeach
                            <option value="Food & Refreshment">
                            <option value="Venue & Meeting">
                            <option value="Stationery">
                            <option value="Utility Bills">
                            <option value="Event Cost">
                            <option value="Bank Charge">
                            <option value="Others">
                        </datalist>
                    </div>
                    <div>
                        <label class="form-label">Amount (৳) <span class="form-required">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount') }}" required
                               min="0.01" step="0.01" placeholder="0.00" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            @foreach(['cash' => 'Cash', 'bank' => 'Bank Transfer', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket', 'other' => 'Other'] as $v => $l)
                            <option value="{{ $v }}" {{ old('payment_method') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Paid By</label>
                        <input type="text" name="paid_by" value="{{ old('paid_by') }}"
                               placeholder="Person who paid" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Voucher / Attachment</label>
                        <input type="file" name="attachment" accept="image/jpeg,image/png,image/jpg,application/pdf"
                               class="form-input file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="form-hint">JPG, PNG or PDF, max 2MB</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Description <span class="form-required">*</span></label>
                        <textarea name="description" rows="3" required
                                  placeholder="What was this expense for?" class="form-textarea">{{ old('description') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Internal Notes</label>
                        <textarea name="notes" rows="2" placeholder="Optional internal notes" class="form-textarea">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary flex-1 justify-center">
                        <i class="fas fa-save"></i> Save Expense
                    </button>
                    <a href="{{ route('admin.expenses.index') }}" class="btn-secondary flex-1 justify-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
