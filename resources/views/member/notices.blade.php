@extends('layouts.app')
@section('title', 'Notices')
@section('page-title', 'Notices & Announcements')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="space-y-4 max-w-screen-md">

    @forelse($notices as $notice)
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex items-start gap-4 p-5">
            <div class="shrink-0 mt-0.5">
                @php
                    $iconMap = [
                        'general'   => ['bg' => 'bg-blue-100',   'text' => 'text-blue-600',   'icon' => 'fa-bullhorn'],
                        'urgent'    => ['bg' => 'bg-red-100',    'text' => 'text-red-600',    'icon' => 'fa-triangle-exclamation'],
                        'financial' => ['bg' => 'bg-emerald-100','text' => 'text-emerald-600','icon' => 'fa-bangladeshi-taka-sign'],
                        'event'     => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'fa-calendar-star'],
                        'meeting'   => ['bg' => 'bg-amber-100',  'text' => 'text-amber-600',  'icon' => 'fa-users'],
                    ];
                    $style = $iconMap[$notice->type] ?? $iconMap['general'];
                @endphp
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl {{ $style['bg'] }} {{ $style['text'] }}">
                    <i class="fas {{ $style['icon'] }}"></i>
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-start justify-between gap-2 mb-1">
                    <h3 class="font-semibold text-slate-800 text-base leading-snug">{{ $notice->title }}</h3>
                    <span class="text-xs text-slate-400 shrink-0">{{ $notice->published_at->format('d M Y') }}</span>
                </div>
                @if($notice->type !== 'general')
                <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full mb-2
                    {{ $notice->type === 'urgent' ? 'bg-red-100 text-red-700' : '' }}
                    {{ $notice->type === 'financial' ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $notice->type === 'event' ? 'bg-purple-100 text-purple-700' : '' }}
                    {{ $notice->type === 'meeting' ? 'bg-amber-100 text-amber-700' : '' }}">
                    {{ ucfirst($notice->type) }}
                </span>
                @endif
                <div class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ $notice->content }}</div>
                @if($notice->publisher)
                <p class="text-xs text-slate-400 mt-3">— {{ $notice->publisher->name }}</p>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-16 text-slate-400">
        <i class="fas fa-bell-slash text-4xl mb-3 block"></i>
        <p class="text-base">No notices at this time.</p>
    </div>
    @endforelse

    <div class="pt-2">
        {{ $notices->links() }}
    </div>
</div>
@endsection
