@extends('layouts.public')
@section('title', 'Contact — Unity Club')
@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Contact Us</h1>
    <p class="text-gray-500 mb-8">Have questions about Unity Club or your membership? Reach out to our admin team.</p>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 space-y-5">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-envelope text-blue-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Email</p>
                <p class="text-gray-800 font-medium">admin@unityclub.local</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-phone text-green-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium">Phone</p>
                <p class="text-gray-800 font-medium">+880 1700-000001</p>
            </div>
        </div>
    </div>
    <div class="mt-8 text-center">
        <a href="{{ route('apply') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
            Apply for Membership
        </a>
    </div>
</div>
@endsection
