<x-guest-layout>
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
        <p class="text-sm text-amber-800 font-medium">
            <i class="fas fa-lock mr-1"></i>
            You must set a new password before you can continue.
        </p>
    </div>

    @if(session('warning'))
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-800">
            {{ session('warning') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.change.update') }}">
        @csrf

        <div class="mb-1 text-lg font-bold text-slate-800">Set Your New Password</div>
        <p class="text-sm text-slate-500 mb-5">Choose a strong password (at least 8 characters with letters and numbers).</p>

        <div>
            <x-input-label for="current_password" :value="__('Current Password')" />
            <x-text-input id="current_password" class="block mt-1 w-full"
                          type="password" name="current_password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="text-sm text-slate-500 hover:text-slate-700 underline">
                Log Out
            </a>
            <x-primary-button>
                {{ __('Set Password') }}
            </x-primary-button>
        </div>
    </form>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>
</x-guest-layout>
