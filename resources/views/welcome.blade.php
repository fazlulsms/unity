<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unity Circle</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-slate-900 flex items-center justify-center p-4">

    <div class="text-center">

        <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-900/50 mx-auto mb-6">
            <span class="text-white font-bold text-3xl tracking-tight">UC</span>
        </div>

        <h1 class="text-white font-bold text-3xl mb-2">Unity Circle</h1>
        <p class="text-slate-400 text-base mb-8">Member Management Portal</p>

        @auth
        <a href="{{ url('/dashboard') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition-colors shadow-lg">
            Go to Dashboard
        </a>
        @else
        <a href="{{ route('login') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition-colors shadow-lg">
            Sign In
        </a>
        @endauth

        <p class="text-slate-600 text-xs mt-10">&copy; {{ date('Y') }} Unity Circle. All rights reserved.</p>

    </div>

</body>
</html>
