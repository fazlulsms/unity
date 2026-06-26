@extends('layouts.app')
@section('title', 'User Management')
@section('page-title', 'User Management')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5">

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Name, email, phone…"
                   class="input-field w-56 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Role</label>
            <select name="role" class="input-field text-sm">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary text-sm">Filter</button>
        @if(request('search') || request('role'))
            <a href="{{ route('admin.users.index') }}" class="btn-secondary text-sm">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left">
                    <th class="table-th">User</th>
                    <th class="table-th">Email</th>
                    <th class="table-th">Role</th>
                    <th class="table-th">Status</th>
                    <th class="table-th">Joined</th>
                    <th class="table-th text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="table-td">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user->photo_url }}" alt="" class="w-8 h-8 rounded-full object-cover shrink-0">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="font-medium text-slate-800 hover:text-blue-600">{{ $user->name }}</a>
                        </div>
                    </td>
                    <td class="table-td text-slate-500">
                        @if(str_ends_with($user->email, '@unity.local'))
                            <span class="italic text-slate-400 text-xs">No email (local)</span>
                        @else
                            {{ $user->email }}
                        @endif
                    </td>
                    <td class="table-td">
                        @foreach($user->roles as $role)
                            <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full
                                {{ $role->name === 'admin' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $role->name === 'treasurer' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ $role->name === 'member' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $role->name === 'super_admin' ? 'bg-amber-100 text-amber-700' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </span>
                        @endforeach
                        @if($user->roles->isEmpty())
                            <span class="text-slate-400 text-xs italic">No role</span>
                        @endif
                    </td>
                    <td class="table-td">
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                            {{ ($user->status ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            <i class="fas fa-circle text-[7px]"></i>
                            {{ ucfirst($user->status ?? 'active') }}
                        </span>
                    </td>
                    <td class="table-td text-slate-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="table-td text-right">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn-sm-secondary">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="table-td text-center text-slate-400 py-10">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
