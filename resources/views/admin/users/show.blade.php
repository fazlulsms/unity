@extends('layouts.app')
@section('title', $user->name)
@section('page-title', 'User Details')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-2xl space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left text-xs"></i> Back to Users
    </a>

    {{-- Profile card --}}
    <div class="card p-6">
        <div class="flex items-center gap-4 mb-5">
            <img src="{{ $user->photo_url }}" alt="" class="w-16 h-16 rounded-xl object-cover">
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">
                    {{ str_ends_with($user->email, '@unity.local') ? 'No email address (local account)' : $user->email }}
                </p>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @foreach($user->roles as $role)
                        <span class="badge
                            {{ $role->name === 'admin' ? 'bg-red-50 text-red-700 ring-1 ring-red-200' : '' }}
                            {{ $role->name === 'treasurer' ? 'bg-purple-50 text-purple-700 ring-1 ring-purple-200' : '' }}
                            {{ $role->name === 'member' ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' : '' }}
                            {{ $role->name === 'super_admin' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </span>
                    @endforeach
                    <span class="badge {{ ($user->status ?? 'active') === 'active' ? 'badge-active' : 'badge-inactive' }}">
                        {{ ucfirst($user->status ?? 'active') }}
                    </span>
                </div>
            </div>
        </div>

        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div>
                <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide">Phone</dt>
                <dd class="text-gray-700 mt-0.5">{{ $user->phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide">Registered</dt>
                <dd class="text-gray-700 mt-0.5">{{ $user->created_at->format('d M Y') }}</dd>
            </div>
            @if($user->member)
            <div>
                <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide">Member Number</dt>
                <dd class="text-gray-700 mt-0.5">
                    <a href="{{ route('admin.members.show', $user->member) }}" class="text-blue-600 hover:underline font-mono">
                        {{ $user->member->member_number }}
                    </a>
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 font-medium uppercase tracking-wide">Member Status</dt>
                <dd class="text-gray-700 mt-0.5">{{ ucfirst($user->member->status) }}</dd>
            </div>
            @endif
        </dl>
    </div>

    {{-- Role management --}}
    <div class="card p-6">
        <h3 class="font-semibold text-gray-700 mb-4">Change Role</h3>
        @if($user->id === auth()->id())
            <p class="text-sm text-gray-500 italic">You cannot change your own role.</p>
        @else
            <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex gap-3 items-end flex-wrap">
                @csrf @method('PATCH')
                <div>
                    <label class="form-label">New Role</label>
                    <select name="role" class="form-select text-sm" required>
                        @foreach(\Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary"
                        onclick="return confirm('Change role for {{ addslashes($user->name) }}?')">
                    Update Role
                </button>
            </form>
        @endif
    </div>

    {{-- Account actions --}}
    <div class="card p-6">
        <h3 class="font-semibold text-gray-700 mb-4">Account Actions</h3>
        <div class="flex flex-wrap gap-3">
            @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            onclick="return confirm('Toggle account status for {{ addslashes($user->name) }}?')"
                            class="{{ ($user->status ?? 'active') === 'active' ? 'btn-warning' : 'btn-success' }}">
                        <i class="fas {{ ($user->status ?? 'active') === 'active' ? 'fa-ban' : 'fa-circle-check' }}"></i>
                        {{ ($user->status ?? 'active') === 'active' ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>
            @endif

            @if(!str_ends_with($user->email, '@unity.local'))
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Reset password for {{ addslashes($user->name) }} and email new credentials?')"
                            class="btn-secondary">
                        <i class="fas fa-key"></i> Reset Password & Email
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection
