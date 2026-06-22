@extends('layouts.app')
@section('title', 'Bulk Collection Entry')
@section('page-title', 'Bulk Monthly Collection')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    {{-- Period selector --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0,0,0,$m,1)) }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="btn btn-md btn-secondary">
                    <i class="fas fa-arrows-rotate"></i> Load Members
                </button>
            </form>
        </div>
    </div>

    {{-- Bulk entry form --}}
    <form action="{{ route('admin.collections.bulk-store') }}" method="POST">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year"  value="{{ $year }}">

        <div class="table-wrap">
            <div class="card-header">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">
                        {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }} — Active Members ({{ $members->count() }})
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Fill amount for members who paid. Rows already recorded are locked.
                    </p>
                </div>
                <button type="submit" class="btn btn-md btn-success">
                    <i class="fas fa-floppy-disk"></i> Save All
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="th">Member</th>
                            <th class="th text-right">Monthly Fee</th>
                            <th class="th">Amount Paid (৳)</th>
                            <th class="th">Method</th>
                            <th class="th">Date</th>
                            <th class="th hidden lg:table-cell">Reference</th>
                            <th class="th text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                        @php $paid = $existing->get($member->id); @endphp
                        <tr class="tr {{ $paid ? 'bg-emerald-50/50' : '' }}">
                            <td class="td">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $member->user->photo_url }}"
                                         class="w-7 h-7 rounded-full object-cover border border-gray-200 shrink-0" alt="">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $member->user->name }}</p>
                                        <p class="text-xs text-gray-400 font-mono">{{ $member->member_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="td text-right text-gray-500 font-mono">
                                ৳ {{ number_format($member->monthly_fee_amount, 2) }}
                            </td>

                            @if($paid)
                            {{-- Already recorded: show locked row --}}
                            <td class="td text-emerald-700 font-semibold">৳ {{ number_format($paid->amount, 2) }}</td>
                            <td class="td text-gray-500 capitalize">{{ $paid->payment_method }}</td>
                            <td class="td text-gray-500 text-xs">{{ $paid->payment_date->format('d M Y') }}</td>
                            <td class="td hidden lg:table-cell text-gray-400 text-xs font-mono">
                                {{ $paid->transaction_reference ?: '—' }}
                            </td>
                            <td class="td text-center">
                                <span class="text-xs font-semibold text-emerald-600">
                                    <i class="fas fa-check-circle"></i> Recorded
                                </span>
                            </td>
                            @else
                            {{-- Entry row --}}
                            <td class="td">
                                <input type="number"
                                       name="entries[{{ $member->id }}][amount]"
                                       value="{{ old("entries.{$member->id}.amount", $member->monthly_fee_amount) }}"
                                       min="0" step="0.01"
                                       class="form-input w-28 text-sm py-1"
                                       placeholder="0.00">
                            </td>
                            <td class="td">
                                <select name="entries[{{ $member->id }}][payment_method]" class="form-select text-sm py-1">
                                    @foreach(['cash','bank','bkash','nagad','rocket','other'] as $method)
                                    <option value="{{ $method }}">{{ ucfirst($method) }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="td">
                                <input type="date"
                                       name="entries[{{ $member->id }}][payment_date]"
                                       value="{{ old("entries.{$member->id}.payment_date", now()->format('Y-m-d')) }}"
                                       class="form-input text-sm py-1">
                            </td>
                            <td class="td hidden lg:table-cell">
                                <input type="text"
                                       name="entries[{{ $member->id }}][reference]"
                                       value="{{ old("entries.{$member->id}.reference") }}"
                                       placeholder="Ref (optional)"
                                       class="form-input text-xs py-1 w-28">
                            </td>
                            <td class="td text-center">
                                <span class="text-xs text-gray-300">—</span>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-4 border-t border-gray-100 flex justify-between items-center">
                <p class="text-xs text-gray-400">
                    Leave amount empty (or 0) to skip a member. Already recorded rows are ignored automatically.
                </p>
                <button type="submit" class="btn btn-md btn-success">
                    <i class="fas fa-floppy-disk"></i> Save All
                </button>
            </div>
        </div>
    </form>

    <a href="{{ route('admin.collections.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to Collections
    </a>
</div>
@endsection
