<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Unity Circle')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white antialiased" x-data="{ mobileMenuOpen: false }">

{{-- ── Navigation ──────────────────────────────────── --}}
<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
                <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shadow-md shadow-blue-200">
                    <span class="text-white font-extrabold text-sm">UC</span>
                </div>
                <span class="font-extrabold text-gray-900 text-lg tracking-tight">Unity Circle</span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}"         class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('about') }}"        class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                <a href="{{ route('events') }}"       class="nav-link {{ request()->routeIs('events') ? 'active' : '' }}">Events</a>
                <a href="{{ route('notices') }}"      class="nav-link {{ request()->routeIs('notices') ? 'active' : '' }}">Announcements</a>
                <a href="{{ route('apply') }}"        class="nav-link {{ request()->routeIs('apply') ? 'active' : '' }}">Membership</a>
                <a href="{{ route('contact') }}"      class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
            </div>

            {{-- Auth buttons --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @if(auth()->user()->isAdminOrTreasurer())
                        <a href="{{ route('admin.dashboard') }}" class="btn-primary btn-sm">
                            <i class="fas fa-gauge-high"></i> Admin Panel
                        </a>
                    @else
                        <a href="{{ route('member.dashboard') }}" class="btn-primary btn-sm">
                            <i class="fas fa-user"></i> My Portal
                        </a>
                    @endif
                @else
                    <a href="{{ route('apply') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                        Join Now
                    </a>
                    <a href="{{ route('login') }}" class="btn-primary btn-sm">
                        <i class="fas fa-right-to-bracket"></i> Member Login
                    </a>
                @endauth
            </div>

            {{-- Mobile toggle --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                <i :class="mobileMenuOpen ? 'fas fa-times' : 'fas fa-bars'" class="text-base"></i>
            </button>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileMenuOpen" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden border-t border-gray-100 py-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Home</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">About</a>
            <a href="{{ route('events') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Events</a>
            <a href="{{ route('notices') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Announcements</a>
            <a href="{{ route('apply') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Membership</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Contact</a>
            <div class="pt-2 border-t border-gray-100 mt-2 flex flex-col gap-2">
                @auth
                    @if(auth()->user()->isAdminOrTreasurer())
                        <a href="{{ route('admin.dashboard') }}" class="btn-primary w-full justify-center">Admin Panel</a>
                    @else
                        <a href="{{ route('member.dashboard') }}" class="btn-primary w-full justify-center">My Portal</a>
                    @endif
                @else
                    <a href="{{ route('apply') }}" class="btn-secondary w-full justify-center">Apply for Membership</a>
                    <a href="{{ route('login') }}" class="btn-primary w-full justify-center">Member Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success'))
<div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4">
    <div class="alert-success">
        <i class="fas fa-circle-check shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
</div>
@endif
@if(session('error'))
<div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4">
    <div class="alert-error">
        <i class="fas fa-circle-exclamation shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
</div>
@endif

<main class="flex-1">
    @yield('content')
</main>

{{-- ── Footer ─────────────────────────────────────── --}}
<footer class="bg-slate-900 text-slate-400 mt-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
        <div class="grid md:grid-cols-4 gap-8 mb-10">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-extrabold text-sm">UC</span>
                    </div>
                    <span class="text-white font-extrabold text-lg">Unity Circle</span>
                </div>
                <p class="text-sm leading-relaxed max-w-xs">
                    A private community of trusted professionals bound by friendship, family values, and a lifelong commitment to one another.
                </p>
            </div>
            <div>
                <h3 class="text-white font-semibold text-sm mb-4">Quick Links</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About Us</a></li>
                    <li><a href="{{ route('events') }}" class="hover:text-white transition-colors">Events</a></li>
                    <li><a href="{{ route('notices') }}" class="hover:text-white transition-colors">Announcements</a></li>
                    <li><a href="{{ route('apply') }}" class="hover:text-white transition-colors">Membership</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-semibold text-sm mb-4">Member Area</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Member Login</a></li>
                    <li><a href="{{ route('apply') }}" class="hover:text-white transition-colors">Apply for Membership</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact Us</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <p>&copy; {{ date('Y') }} Unity Circle. All rights reserved.</p>
            <p class="text-slate-600">Private Membership Club</p>
        </div>
    </div>
</footer>

</body>
</html>
