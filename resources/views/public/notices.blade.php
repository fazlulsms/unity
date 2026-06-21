@extends('layouts.public')
@section('title', 'Notices — Unity Circle')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">Public Notices & Announcements</h1>

    <div class="space-y-4">
        @forelse($notices as $notice)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-medium bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ ucfirst($notice->type) }}</span>
                        <span class="text-xs text-gray-400">{{ $notice->published_at?->format('d M Y') }}</span>
                    </div>
                    <h2 class="font-semibold text-gray-900 mb-2">{{ $notice->title }}</h2>
                    <div class="text-sm text-gray-600 prose prose-sm max-w-none">{!! nl2br(e($notice->content)) !!}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-bell-slash text-4xl mb-4"></i>
            <p>No notices published yet.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $notices->links() }}</div>
</div>
@endsection
