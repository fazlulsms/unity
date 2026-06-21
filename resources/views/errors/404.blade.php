@extends('layouts.public')
@section('title', 'Page Not Found — Unity Circle')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-20">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 bg-blue-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-compass text-blue-600 text-3xl"></i>
        </div>
        <h1 class="text-7xl font-extrabold text-gray-200 mb-2">404</h1>
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Page Not Found</h2>
        <p class="text-gray-500 mb-8 leading-relaxed">
            The page you're looking for doesn't exist or may have been moved.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}" class="btn-primary">
                <i class="fas fa-house"></i> Back to Home
            </a>
            <a href="{{ route('contact') }}" class="btn-secondary">
                <i class="fas fa-envelope"></i> Contact Us
            </a>
        </div>
    </div>
</div>
@endsection
