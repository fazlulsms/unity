@extends('layouts.public')
@section('title', 'Session Expired — Unity Circle')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-20">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 bg-orange-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-clock text-orange-500 text-3xl"></i>
        </div>
        <h1 class="text-7xl font-extrabold text-gray-200 mb-2">419</h1>
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Session Expired</h2>
        <p class="text-gray-500 mb-8 leading-relaxed">
            Your session has expired or the page token is invalid.
            Please go back and try again.
        </p>
        <a href="javascript:history.back()" class="btn-primary">
            <i class="fas fa-rotate-left"></i> Go Back
        </a>
    </div>
</div>
@endsection
