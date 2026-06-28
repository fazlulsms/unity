{{--
    Reusable period filter.
    Required:  $range (App\Support\DateRange), $action (base URL for this page)
    Optional:  $pdf (PDF/export URL — the current period is appended automatically)
--}}
@php $pdf = $pdf ?? null; @endphp
<div class="card p-3 sm:p-4">
    <div class="flex flex-wrap items-end gap-2 sm:gap-3">
        <form method="GET" action="{{ $action }}" class="flex flex-wrap items-end gap-2 sm:gap-3">
            <div>
                <label class="block text-[11px] font-medium text-gray-500 mb-1">From Date</label>
                <input type="date" name="from" value="{{ $range->from?->toDateString() }}"
                       class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-[11px] font-medium text-gray-500 mb-1">To Date</label>
                <input type="date" name="to" value="{{ $range->to?->toDateString() }}"
                       class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition-colors">Apply</button>
            <a href="{{ $action }}" class="border border-gray-300 text-gray-700 px-4 py-1.5 rounded-lg text-sm hover:bg-gray-50 transition-colors">Reset</a>
        </form>
        @if($pdf)
        <a href="{{ $pdf }}{{ str_contains($pdf, '?') ? '&' : '?' }}{{ $range->queryString() }}"
           class="ml-auto inline-flex items-center gap-1.5 bg-violet-600 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-violet-700 transition-colors">
            <i class="fas fa-download"></i> Download PDF
        </a>
        @endif
    </div>

    <div class="flex flex-wrap items-center gap-1.5 mt-3">
        @foreach(\App\Support\DateRange::PRESETS as $key => $label)
        <a href="{{ $action }}?preset={{ $key }}"
           class="px-3 py-1 rounded-full text-xs font-medium transition-colors {{ $range->preset === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $label }}
        </a>
        @endforeach
        <span class="ml-auto text-xs text-gray-400">Showing: <strong class="text-gray-600">{{ $range->label }}</strong></span>
    </div>
</div>
