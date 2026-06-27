@extends('layouts.app')
@section('title', 'Update History — ' . $member->user->name)
@section('page-title', 'Profile Update History')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5">

    {{-- Page header --}}
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <img src="{{ $member->user->photo_url }}"
                     class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0" alt="">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base font-bold text-gray-900">{{ $member->user->name }}</h2>
                        <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $member->member_number }}</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">Profile Update History</p>
                </div>
                <a href="{{ route('admin.members.show', $member) }}" class="btn btn-sm btn-secondary shrink-0">
                    ← Back to Profile
                </a>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.members.history', $member) }}"
                  class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit" class="btn btn-sm btn-secondary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    @if(request('date_from') || request('date_to'))
                    <a href="{{ route('admin.members.history', $member) }}" class="btn btn-sm btn-ghost text-gray-500">
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Results --}}
    @if($histories->isEmpty())
    <div class="card">
        <div class="card-body text-center py-12">
            <i class="fas fa-history text-4xl text-gray-200 mb-3 block"></i>
            <p class="text-sm text-gray-400 font-medium">No profile updates found</p>
            @if(request('date_from') || request('date_to'))
            <p class="text-xs text-gray-400 mt-1">Try adjusting the date filter.</p>
            @else
            <p class="text-xs text-gray-400 mt-1">Profile edits will appear here after the first update.</p>
            @endif
        </div>
    </div>
    @else
    <div class="card overflow-hidden">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm">
                <i class="fas fa-history text-gray-400 mr-1.5"></i> Update Records
            </p>
            <span class="text-xs text-gray-400">{{ $histories->total() }} record(s)</span>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($histories as $history)
            <div class="px-5 py-4">
                {{-- Row header: who + when --}}
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 shrink-0">
                            <i class="fas fa-user-edit text-blue-500 text-xs"></i>
                        </span>
                        <div>
                            <p class="text-xs font-semibold text-gray-800">{{ $history->updater?->name ?? 'System' }}</p>
                            <p class="text-xs text-gray-400">{{ $history->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 bg-gray-50 border border-gray-100 rounded px-2 py-0.5">
                        {{ count($history->changes) }} field(s) changed
                    </span>
                </div>

                {{-- Changed fields table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left text-gray-400 font-medium pb-1.5 pr-4 w-32">Field</th>
                                <th class="text-left text-gray-400 font-medium pb-1.5 pr-4">Old Value</th>
                                <th class="text-left text-gray-400 font-medium pb-1.5 w-4"></th>
                                <th class="text-left text-gray-400 font-medium pb-1.5">New Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($history->changes as $label => $change)
                            <tr>
                                <td class="py-1.5 pr-4 font-semibold text-gray-600 align-top">{{ $label }}</td>
                                <td class="py-1.5 pr-4 align-top">
                                    <span class="{{ $change['old'] !== '' ? 'text-red-500 line-through' : 'text-gray-300 italic' }}">
                                        {{ $change['old'] !== '' ? $change['old'] : 'empty' }}
                                    </span>
                                </td>
                                <td class="py-1.5 pr-3 text-gray-300 align-top">→</td>
                                <td class="py-1.5 align-top">
                                    <span class="{{ $change['new'] !== '' ? 'text-emerald-600 font-medium' : 'text-gray-300 italic' }}">
                                        {{ $change['new'] !== '' ? $change['new'] : 'cleared' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>

        @if($histories->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $histories->links() }}
        </div>
        @endif
    </div>
    @endif

</div>
@endsection
