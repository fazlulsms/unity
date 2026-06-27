@extends('layouts.app')
@section('title', 'Email History — ' . $member->user->name)
@section('page-title', 'Email History')
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
                    <p class="text-xs text-gray-400 mt-0.5">{{ $member->user->email }}</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    @if(!str_ends_with($member->user->email ?? '', '@unity.local') && $member->user->email)
                    <a href="{{ route('admin.members.show', $member) }}#email-actions"
                       class="btn btn-sm btn-secondary">
                        <i class="fas fa-envelope"></i> Send Email
                    </a>
                    @endif
                    <a href="{{ route('admin.members.show', $member) }}" class="btn btn-sm btn-secondary">
                        ← Back to Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter / search bar --}}
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.members.emails', $member) }}"
                  class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-48">
                    <label class="form-label">Search Subject</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by subject…" class="form-input">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="">All</option>
                        <option value="sent"   {{ request('status') === 'sent'   ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit" class="btn btn-sm btn-secondary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('admin.members.emails', $member) }}" class="btn btn-sm btn-ghost text-gray-500">
                        Clear
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Email log table --}}
    <div class="card overflow-hidden">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm">
                <i class="fas fa-envelope text-gray-400 mr-1.5"></i> Emails
            </p>
            <span class="text-xs text-gray-400">{{ $emailLogs->total() }} record(s)</span>
        </div>

        @if($emailLogs->isEmpty())
        <div class="px-5 py-12 text-center">
            <i class="fas fa-envelope-open text-4xl text-gray-200 mb-3 block"></i>
            <p class="text-sm text-gray-400 font-medium">No emails found</p>
            @if(request('search') || request('status'))
            <p class="text-xs text-gray-400 mt-1">Try adjusting the search or status filter.</p>
            @else
            <p class="text-xs text-gray-400 mt-1">Emails sent to this member will appear here.</p>
            @endif
        </div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Subject</th>
                    <th class="th">Status</th>
                    <th class="th hidden sm:table-cell">Sent By</th>
                    <th class="th hidden md:table-cell text-right">Date & Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($emailLogs as $log)
                <tr class="tr">
                    <td class="td">
                        <p class="text-gray-800 font-medium text-sm">{{ $log->subject ?: '(no subject)' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $log->mailable_short_name }}</p>
                        @if($log->status === 'failed' && $log->error_message)
                        <p class="text-xs text-red-500 font-mono mt-1 break-all">{{ Str::limit($log->error_message, 100) }}</p>
                        @endif
                    </td>
                    <td class="td">
                        @if($log->status === 'sent')
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 ring-1 ring-emerald-200 px-2 py-0.5 rounded-full">
                            <i class="fas fa-check text-[9px]"></i> Sent
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-50 ring-1 ring-red-200 px-2 py-0.5 rounded-full">
                            <i class="fas fa-times text-[9px]"></i> Failed
                        </span>
                        @endif
                    </td>
                    <td class="td hidden sm:table-cell text-gray-500 text-xs">
                        {{ $log->sender?->name ?? 'System' }}
                    </td>
                    <td class="td hidden md:table-cell text-right text-gray-400 text-xs whitespace-nowrap">
                        {{ $log->created_at->format('d M Y') }}<br>
                        <span class="text-gray-300">{{ $log->created_at->format('h:i A') }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($emailLogs->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $emailLogs->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
