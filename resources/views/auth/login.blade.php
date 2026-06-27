<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — Unity Circle</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-slate-900 flex items-center justify-center p-4">

    <div class="w-full max-w-sm">

        {{-- Brand --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow-xl shadow-blue-900/40 mx-auto mb-4">
                <span class="text-white font-bold text-2xl tracking-tight">UC</span>
            </div>
            <h1 class="text-white font-bold text-2xl leading-tight">Unity Circle</h1>
            <p class="text-slate-400 text-sm mt-1">Member Management Portal</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">

            @if(session('status'))
            <div class="mb-5 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-700">
                {{ session('status') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <h2 class="text-lg font-bold text-gray-900 mb-6">Sign in to your account</h2>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           class="form-input @error('email') border-red-400 @enderror">
                </div>

                <div>
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password"
                           class="form-input @error('password') border-red-400 @enderror">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer select-none">
                        <input type="checkbox" name="remember"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
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
                        class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                               text-white font-semibold rounded-xl transition-colors shadow-sm">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-slate-500 text-xs mt-6">
            &copy; {{ date('Y') }} Unity Circle. All rights reserved.
        </p>

    </div>

</body>
</html>
