<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — Unity Circle</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex bg-gray-50">

    {{-- ── Left panel (hidden on mobile) ───────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-slate-900 flex-col relative overflow-hidden">

        {{-- Decorative blobs --}}
        <div class="absolute top-0 right-0 w-80 h-80 bg-blue-600/20 rounded-full blur-3xl -translate-y-1/3 translate-x-1/3 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl translate-y-1/3 -translate-x-1/3 pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-indigo-600/10 rounded-full blur-2xl -translate-y-1/2 pointer-events-none"></div>

        {{-- Content --}}
        <div class="relative z-10 flex flex-col items-center justify-center flex-1 p-12 text-center">
            <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-900/60 mb-7">
                <span class="text-white font-bold text-3xl tracking-tight">UC</span>
            </div>
            <h1 class="text-white font-bold text-3xl mb-3 leading-tight">Unity Circle</h1>
            <p class="text-slate-400 text-base leading-relaxed max-w-xs">
                Secure member management portal for contributions, finances, and club records.
            </p>

            {{-- Feature highlights --}}
            <div class="mt-12 space-y-3 w-full max-w-xs text-left">
                <div class="flex items-center gap-3 text-slate-400 text-sm">
                    <div class="w-8 h-8 rounded-lg bg-blue-600/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                        </svg>
                    </div>
                    <span>Member profiles &amp; records</span>
                </div>
                <div class="flex items-center gap-3 text-slate-400 text-sm">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span>Monthly contributions &amp; receipts</span>
                </div>
                <div class="flex items-center gap-3 text-slate-400 text-sm">
                    <div class="w-8 h-8 rounded-lg bg-violet-600/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span>Financial reports &amp; statements</span>
                </div>
            </div>
        </div>

        {{-- Bottom credit --}}
        <div class="relative z-10 p-6 text-center">
            <p class="text-slate-600 text-xs">&copy; {{ date('Y') }} Unity Circle</p>
        </div>
    </div>

    {{-- ── Right panel (form) ────────────────────────────────────────────── --}}
    <div class="flex-1 flex items-center justify-center p-8 lg:p-16">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center gap-3 mb-10">
                <div class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shrink-0">
                    <span class="text-white font-bold text-base tracking-tight">UC</span>
                </div>
                <div>
                    <p class="font-bold text-gray-900 text-lg leading-none">Unity Circle</p>
                    <p class="text-gray-400 text-xs mt-0.5">Member Portal</p>
                </div>
            </div>

            {{-- Heading --}}
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
                <p class="text-gray-500 text-sm mt-1">Sign in to your account to continue.</p>
            </div>

            {{-- Alerts --}}
            @if(session('status'))
            <div class="mb-6 flex items-start gap-2 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 flex items-start gap-2 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="you@example.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                  text-sm text-gray-900 placeholder-gray-400 transition
                                  @error('email') border-red-400 bg-red-50 @enderror">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50
                                  focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                  text-sm text-gray-900 placeholder-gray-400 transition
                                  @error('password') border-red-400 bg-red-50 @enderror">
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                        <input type="checkbox" name="remember"
                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        Remember me
                    </label>

                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        Forgot password?
                    </a>
                    @endif
                </div>

                <button type="submit"
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                               text-white font-semibold rounded-xl transition-colors shadow-sm text-sm mt-2">
                    Sign In
                </button>
            </form>

            <p class="text-center text-gray-400 text-xs mt-10 lg:hidden">
                &copy; {{ date('Y') }} Unity Circle. All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>
