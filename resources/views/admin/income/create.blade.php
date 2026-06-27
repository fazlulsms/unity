@extends('layouts.app')
@section('title', 'Add Income')
@section('page-title', 'Add Income')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-lg space-y-4">

    @if($errors->any())
    <div class="alert-error">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.income.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Date <span class="form-required">*</span></label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Income Type <span class="form-required">*</span></label>
                        <select name="income_type" id="income_type" required class="form-input">
                            @foreach(['fdr_interest' => 'FDR Interest', 'donation' => 'Donation', 'extra_contribution' => 'Extra Contribution', 'other' => 'Other'] as $v => $l)
                            <option value="{{ $v }}" {{ old('income_type') === $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FDR selector — shown only for fdr_interest --}}
                    <div id="fdr_row" class="sm:col-span-2 hidden">
                        <label class="form-label">Linked FDR Record <span class="form-required">*</span></label>
                        <select name="fdr_id" id="fdr_id" class="form-input">
                            <option value="">— Select FDR —</option>
                            @foreach($fdrs as $fdr)
                            <option value="{{ $fdr->id }}"
                                    data-source="{{ $fdr->bank_name }}{{ $fdr->branch ? ' – ' . $fdr->branch : '' }} · FDR #{{ $fdr->fdr_number }}"
                                    {{ old('fdr_id') == $fdr->id ? 'selected' : '' }}>
                                {{ $fdr->bank_name }} — FDR #{{ $fdr->fdr_number }} ({{ ucfirst($fdr->status) }})
                            </option>
                            @endforeach
                        </select>
                        @error('fdr_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label">Source <span class="form-required">*</span></label>
                        <input type="text" name="source" id="source" value="{{ old('source') }}" required class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Amount (৳) <span class="form-required">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount') }}" required min="0.01" step="0.01" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Reference</label>
                        <input type="text" name="reference" value="{{ old('reference') }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Attachment</label>
                        <input type="file" name="attachment" accept="image/jpeg,image/png,application/pdf" class="form-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2" class="form-textarea">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-sm btn-primary flex-1">Save Income</button>
                    <a href="{{ route('admin.income.index') }}" class="btn btn-sm btn-secondary flex-1 text-center">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const typeSelect  = document.getElementById('income_type');
    const fdrRow      = document.getElementById('fdr_row');
    const fdrSelect   = document.getElementById('fdr_id');
    const sourceInput = document.getElementById('source');

    function toggle() {
        const isFdr = typeSelect.value === 'fdr_interest';
        fdrRow.classList.toggle('hidden', !isFdr);
        fdrSelect.required = isFdr;
    }

    fdrSelect.addEventListener('change', function () {
        const opt = fdrSelect.options[fdrSelect.selectedIndex];
        if (opt && opt.dataset.source) {
            sourceInput.value = opt.dataset.source;
        }
    });

    typeSelect.addEventListener('change', toggle);
    toggle();
})();
</script>
@endsection
