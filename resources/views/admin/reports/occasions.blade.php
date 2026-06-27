@extends('layouts.app')
@section('title', 'Occasions & Reminders')
@section('page-title', 'Occasions & Reminders')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5 max-w-5xl">

    {{-- Filter bar --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.occasions') }}" class="flex flex-wrap gap-3 items-end">

                <div>
                    <p class="form-label mb-2">Filter Type</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach([
                            'member_birthdays' => 'Member Birthdays',
                            'family_birthdays' => 'Family Birthdays',
                            'anniversaries'    => 'Marriage Anniversaries',
                            'religion'         => 'Members by Religion',
                        ] as $val => $label)
                        <a href="{{ route('admin.reports.occasions', ['filter' => $val, 'days' => $days]) }}"
                           class="btn btn-sm {{ $filter === $val ? 'btn-primary' : 'btn-secondary' }}">
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>

                @if($filter !== 'religion')
                <div class="ml-auto">
                    <label class="form-label" for="days">Look-ahead window</label>
                    <select id="days" name="days" class="form-input"
                            onchange="this.form.submit()">
                        @foreach([30 => '30 days', 60 => '60 days', 90 => '90 days', 180 => '6 months', 365 => '1 year'] as $val => $label)
                        <option value="{{ $val }}" @selected((int)$days === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="filter" value="{{ $filter }}">
                </div>
                @endif

            </form>
        </div>
    </div>

    {{-- Results --}}
    @if($results->isEmpty())
    <div class="card">
        <div class="card-body text-center py-12">
            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-calendar-check text-gray-400 text-lg"></i>
            </div>
            <p class="text-sm font-semibold text-gray-500">No records found</p>
            <p class="text-xs text-gray-400 mt-1">Try expanding the look-ahead window or selecting a different filter.</p>
        </div>
    </div>

    @elseif($filter === 'religion')

    {{-- Religion summary grid --}}
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($results as $r)
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="font-semibold text-gray-900">{{ $r->label }}</p>
                <span class="text-2xl font-bold text-blue-600">{{ $r->count }}</span>
            </div>
            <p class="text-xs text-gray-400 leading-relaxed">{{ $r->members }}</p>
        </div>
        @endforeach
    </div>

    @else

    {{-- Occasion list table --}}
    <div class="table-wrap">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm">
                @if($filter === 'member_birthdays') Member Birthdays
                @elseif($filter === 'family_birthdays') Family Birthdays
                @elseif($filter === 'anniversaries') Marriage Anniversaries
                @endif
                <span class="ml-2 text-xs font-normal text-gray-400">— {{ $results->count() }} result(s) within {{ $days }} days</span>
            </p>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Name</th>
                    <th class="th">Details</th>
                    <th class="th">Date</th>
                    <th class="th">Days Away</th>
                    <th class="th text-right">Link</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $r)
                <tr class="tr">
                    <td class="td">
                        <div class="flex items-center gap-2">
                            @if($r->photo)
                            <img src="{{ $r->photo }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="">
                            @else
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                <i class="fas fa-user text-gray-400 text-xs"></i>
                            </div>
                            @endif
                            <span class="font-medium text-gray-900">{{ $r->label }}</span>
                        </div>
                    </td>
                    <td class="td text-gray-500 text-xs">{{ $r->sub }}</td>
                    <td class="td text-gray-700">{{ $r->date->format('d M') }}</td>
                    <td class="td">
                        @if($r->days === 0)
                        <span class="font-bold text-orange-600">Today!</span>
                        @elseif($r->days <= 7)
                        <span class="font-semibold text-amber-600">in {{ $r->days }} day{{ $r->days === 1 ? '' : 's' }}</span>
                        @else
                        <span class="text-gray-600">in {{ $r->days }} days</span>
                        @endif
                    </td>
                    <td class="td text-right">
                        <a href="{{ $r->link }}" class="btn btn-sm btn-secondary text-xs">View →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif

    <a href="{{ route('admin.reports.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to Reports
    </a>

</div>
@endsection
