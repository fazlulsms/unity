@extends('layouts.app')
@section('title', 'User Management')
@section('page-title', 'User Management')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    {{-- Filters --}}
    <form method="GET" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Name, email, phone…" class="form-input max-w-xs text-sm">
        <select name="role" class="form-select w-auto text-sm">
            <option value="">All Roles</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary btn-sm">Filter</button>
        @if(request('search') || request('role'))
            <a href="{{ route('admin.users.index') }}" class="btn-ghost btn-sm">Clear</a>
        @endif
        <span class="ml-auto text-xs text-gray-400">{{ $users->total() }} users</span>
    </form>

    {{-- Table --}}
    <div class="table-wrap">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">User</th>
                    <th class="th hidden md:table-cell">Email</th>
                    <th class="th">Role</th>
                    <th class="th hidden sm:table-cell">Status</th>
                    <th class="th hidden lg:table-cell">Joined</th>
                    <th class="th text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="tr">
                    <td class="td">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->photo_url }}" alt="" class="w-8 h-8 rounded-full object-cover shrink-0">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="font-medium text-gray-900 hover:text-blue-600 text-sm">{{ $user->name }}</a>
                        </div>
                    </td>
                    <td class="td hidden md:table-cell text-gray-500 text-xs">
                        @if(str_ends_with($user->email, '@unity.local'))
                            <span class="italic text-gray-400">No email (local account)</span>
                        @else
                            {{ $user->email }}
                        @endif
                    </td>
                    <td class="td">
                        @foreach($user->roles as $role)
                            <span class="badge
                                {{ $role->name === 'admin' ? 'bg-red-50 text-red-700 ring-1 ring-red-200' : '' }}
                                {{ $role->name === 'treasurer' ? 'bg-purple-50 text-purple-700 ring-1 ring-purple-200' : '' }}
                                {{ $role->name === 'member' ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' : '' }}
                                {{ $role->name === 'super_admin' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </span>
                        @endforeach
                        @if($user->roles->isEmpty())
                            <span class="text-gray-400 text-xs italic">No role</span>
                        @endif
                    </td>
                    <td class="td hidden sm:table-cell">
                        <span class="badge {{ ($user->status ?? 'active') === 'active' ? 'badge-active' : 'badge-inactive' }}">
                            {{ ucfirst($user->status ?? 'active') }}
                        </span>
                    </td>
                    <td class="td hidden lg:table-cell text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="td text-right">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-secondary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="table-empty">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $users->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
