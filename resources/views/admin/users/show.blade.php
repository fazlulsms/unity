@extends('layouts.app')
@section('title', $user->name)
@section('page-title', 'User Details')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-2xl space-y-5">

    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700">
        <i class="fas fa-arrow-left text-xs"></i> Back to Users
    </a>

    {{-- Profile card --}}
    <div class="card p-6">
        <div class="flex items-center gap-4 mb-5">
            <img src="{{ $user->photo_url }}" alt="" class="w-16 h-16 rounded-xl object-cover">
            <div>
                <h2 class="text-lg font-bold text-slate-800">{{ $user->name }}</h2>
                <p class="text-sm text-slate-500">{{ str_ends_with($user->email, '@unity.local') ? 'No email address' : $user->email }}</p>
                <div class="flex flex-wrap gap-1.5 mt-1.5">
                    @foreach($user->roles as $role)
                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full
                            {{ $role->name === 'admin' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $role->name === 'treasurer' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $role->name === 'member' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $role->name === 'super_admin' ? 'bg-amber-100 text-amber-700' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </span>
                    @endforeach
                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                        {{ ($user->status ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        <i class="fas fa-circle text-[7px]"></i>
                        {{ ucfirst($user->status ?? 'active') }}
                    </span>
                </div>
            </div>
        </div>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Phone</dt>
                <dd class="text-slate-700 mt-0.5">{{ $user->phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Registered</dt>
                <dd class="text-slate-700 mt-0.5">{{ $user->created_at->format('d M Y') }}</dd>
            </div>
            @if($user->member)
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Member Number</dt>
                <dd class="text-slate-700 mt-0.5">
                    <a href="{{ route('admin.members.show', $user->member) }}" class="text-blue-600 hover:underline">
                        {{ $user->member->member_number }}
                    </a>
                </dd>
            </div>
            <div>
                <dt class="text-xs text-slate-400 font-medium uppercase tracking-wide">Member Status</dt>
                <dd class="text-slate-700 mt-0.5">{{ ucfirst($user->member->status) }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Role management --}}
    <div class="card p-6">
        <h3 class="font-semibold text-slate-700 mb-4">Change Role</h3>
        @if($user->id === auth()->id())
            <p class="text-sm text-slate-500 italic">You cannot change your own role.</p>
        @else
            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex gap-3 items-end">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">New Role</label>
                    <select name="role" class="input-field text-sm" required>
                        @foreach(\Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
                            <option value="{{ $role->name }}"
                                {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary text-sm"
                        onclick="return confirm('Change role for {{ $user->name }}?')">
                    Update Role
                </button>
            </form>
        @endif
    </div>

    {{-- Account actions --}}
    <div class="card p-6">
        <h3 class="font-semibold text-slate-700 mb-4">Account Actions</h3>
        <div class="flex flex-wrap gap-3">
            @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            onclick="return confirm('Toggle account status for {{ $user->name }}?')"
                            class="{{ ($user->status ?? 'active') === 'active' ? 'btn-warning' : 'btn-primary' }} text-sm">
                        {{ ($user->status ?? 'active') === 'active' ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>
            @endif

            @if(!str_ends_with($user->email, '@unity.local'))
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                    @csrf @method('POST')
                    <button type="submit"
                            onclick="return confirm('Reset password and email new credentials to {{ $user->name }}?')"
                            class="btn-secondary text-sm">
                        Reset Password & Email
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection
