<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal') — Unity Circle</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 antialiased" x-data="{ sidebarOpen: false }">

{{-- Mobile backdrop --}}
<div x-show="sidebarOpen" x-cloak
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-black/60 lg:hidden"
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
</div>

{{-- ── SIDEBAR: always fixed to viewport ──────────────────────────────── --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-900
              transition-transform duration-300 ease-in-out">

    {{-- Logo --}}
    <div class="shrink-0 flex items-center gap-3 px-5 h-16 border-b border-slate-800">
        <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-900/50 shrink-0">
            <span class="text-white font-bold text-sm tracking-tight">UC</span>
        </div>
        <div class="min-w-0">
            <p class="text-white font-bold text-sm leading-none">Unity Circle</p>
            <p class="text-slate-500 text-xs mt-0.5">Management</p>
        </div>
        <button @click="sidebarOpen = false" class="ml-auto p-1 text-slate-500 hover:text-white lg:hidden">
            <i class="fas fa-times text-sm"></i>
        </button>
    </div>

    {{-- Navigation: only scrolls when menu exceeds viewport height --}}
    <nav class="flex-1 min-h-0 overflow-y-auto px-3 py-4 space-y-0.5 scrollbar-hide">
        @yield('sidebar')
    </nav>

    {{-- User footer --}}
    <div class="shrink-0 border-t border-slate-800 p-3">
        <div class="flex items-center gap-3 px-2 py-2 rounded-lg">
            <img src="{{ auth()->user()->photo_url }}" alt="avatar"
                 class="w-8 h-8 rounded-full object-cover ring-2 ring-slate-700 shrink-0">
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-white truncate leading-none">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 capitalize mt-0.5">{{ auth()->user()->getRoleNames()->first() ?? 'user' }}</p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="mt-1">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2.5 px-2 py-2 text-xs font-medium text-slate-400
                           hover:text-red-400 hover:bg-slate-800 rounded-lg transition-colors">
                <i class="fas fa-arrow-right-from-bracket w-4"></i>
                Sign out
            </button>
        </form>
    </div>
</aside>

{{-- ── MAIN: offset by sidebar width, natural document scroll ─────────── --}}
<div class="lg:pl-64 flex flex-col min-h-screen">

    {{-- Sticky top header --}}
    <header class="sticky top-0 z-30 shrink-0 flex items-center gap-4 h-16
                   bg-white border-b border-gray-200 px-6">
        {{-- Mobile menu toggle --}}
        <button @click="sidebarOpen = true"
                class="p-2 -ml-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 lg:hidden transition-colors">
            <i class="fas fa-bars text-sm"></i>
        </button>

        {{-- Page title --}}
        <h1 class="text-base font-semibold text-gray-900 flex-1 truncate">
            @yield('page-title', 'Dashboard')
        </h1>

        {{-- Right side --}}
        <div class="flex items-center gap-4 shrink-0">
            <span class="hidden sm:flex items-center gap-1.5 text-xs text-gray-400 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5">
                <i class="fas fa-calendar-days text-gray-300"></i>
                {{ now()->format('d M Y') }}
            </span>
            <div class="flex items-center gap-2">
                <img src="{{ auth()->user()->photo_url }}" alt="avatar"
                     class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-200">
                <span class="hidden md:block text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </header>

    {{-- Flash messages (layout-level) --}}
    @if(session('success'))
    <div class="px-6 pt-5">
        <div class="alert-success">
            <i class="fas fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="px-6 pt-5">
        <div class="alert-error">
            <i class="fas fa-circle-exclamation text-red-500 mt-0.5 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif
    @if(session('warning'))
    <div class="px-6 pt-5">
        <div class="alert-warning">
            <i class="fas fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0"></i>
            <span>{{ session('warning') }}</span>
        </div>
    </div>
    @endif

    {{-- Page content: scrolls naturally with the browser --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>
</div>

</body>
</html>
