@extends('layouts.app')
@section('title', 'Notices')
@section('page-title', 'Notices & Announcements')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-4">
    <div class="flex justify-end">
        <a href="{{ route('admin.notices.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">+ New Notice</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Title</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Type</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Published</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Public</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($notices as $n)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $n->title }}</td>
                    <td class="px-5 py-3"><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">{{ ucfirst($n->type) }}</span></td>
                    <td class="px-5 py-3 text-gray-500">{{ $n->published_at?->format('d M Y') ?? 'Draft' }}</td>
                    <td class="px-5 py-3">
                        @if($n->is_public) <span class="badge-active">Public</span> @else <span class="badge-voided">Members Only</span> @endif
                    </td>
                    <td class="px-5 py-3 flex gap-2">
                        <a href="{{ route('admin.notices.edit', $n) }}" class="text-blue-600 text-xs hover:underline">Edit</a>
                        <form action="{{ route('admin.notices.destroy', $n) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-500 text-xs hover:underline" onclick="return confirm('Delete notice?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No notices.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($notices->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $notices->links() }}</div>
        @endif
    </div>
</div>
@endsection
