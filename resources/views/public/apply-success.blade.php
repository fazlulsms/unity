@extends('layouts.public')
@section('title', 'Application Submitted — Unity Club')

@section('content')
<div class="max-w-lg mx-auto px-4 py-20 text-center">
    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-check-circle text-green-600 text-4xl"></i>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 mb-3">Application Submitted!</h1>
    <p class="text-gray-500 mb-6">
        Thank you for applying to Unity Club. Our admin team will review your application
        and contact you via phone or email within a few business days.
    </p>
    <div class="bg-blue-50 rounded-xl p-5 text-sm text-blue-800 border border-blue-100 mb-8">
        <strong>What happens next?</strong>
        <ul class="mt-2 text-left list-disc list-inside space-y-1 text-blue-700">
            <li>Admin reviews your application</li>
            <li>You will be contacted for confirmation</li>
            <li>On approval, a member account is created for you</li>
            <li>You will receive login credentials via email</li>
        </ul>
    </div>
    <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
        Return to Home
    </a>
</div>
@endsection
