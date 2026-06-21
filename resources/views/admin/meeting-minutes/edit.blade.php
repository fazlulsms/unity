@extends('layouts.app')
@section('title', 'Edit Meeting Minutes')
@section('page-title', 'Edit Meeting Minutes')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <form action="{{ route('admin.meeting-minutes.update', $meetingMinute) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title', $meetingMinute->title) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Meeting Date</label>
                    <input type="date" name="meeting_date" value="{{ old('meeting_date', $meetingMinute->meeting_date->format('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">New Attachment</label>
                    <input type="file" name="attachment" accept="image/jpeg,image/png,application/pdf,.doc,.docx"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @if($meetingMinute->attachment)
                    <p class="text-xs text-gray-400 mt-1"><a href="{{ $meetingMinute->attachment_url }}" target="_blank" class="text-blue-600">Current file</a></p>
                    @endif
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Content</label>
                <textarea name="content" rows="10" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">{{ old('content', $meetingMinute->content) }}</textarea>
            </div>
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public', $meetingMinute->is_public) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300">
                    <span class="text-sm text-gray-700">Make visible to all members</span>
                </label>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm">Update Minutes</button>
                <a href="{{ route('admin.meeting-minutes.index') }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2.5 rounded-lg text-sm hover:bg-gray-50 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
