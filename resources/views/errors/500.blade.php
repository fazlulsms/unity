@extends('layouts.public')
@section('title', 'Server Error — Unity Circle')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-20">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 bg-red-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-triangle-exclamation text-red-500 text-3xl"></i>
        </div>
        <h1 class="text-7xl font-extrabold text-gray-200 mb-2">500</h1>
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Something Went Wrong</h2>
        <p class="text-gray-500 mb-8 leading-relaxed">
            We're experiencing a technical issue. Our team has been notified.
            Please try again in a moment.
        </p>
        <a href="{{ route('home') }}" class="btn-primary">
            <i class="fas fa-house"></i> Back to Home
        </a>
    </div>
</div>
@endsection
