@extends('layouts.public')
@section('title', 'About — Unity Circle')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-4">About Unity Circle</h1>
    <div class="w-16 h-1 bg-blue-600 rounded mb-8"></div>

    <div class="prose prose-lg max-w-none text-gray-600 space-y-6">
        <p>
            Unity Circle is a small, private friendship club founded on the principles of trust, mutual support, and financial transparency. Our members come together with the shared goal of building collective savings and supporting each other through a structured, accountable system.
        </p>

        <div class="grid sm:grid-cols-2 gap-6 not-prose my-8">
            @foreach([
                ['title' => 'Our Mission', 'icon' => 'bullseye', 'text' => 'To provide a transparent and accountable platform for collective savings and financial growth among trusted friends.'],
                ['title' => 'Our Values', 'icon' => 'heart', 'text' => 'Honesty, transparency, accountability, and mutual respect are the cornerstones of our club.'],
                ['title' => 'How We Work', 'icon' => 'cogs', 'text' => 'Members contribute monthly fees which are managed transparently. Funds are invested in FDRs when sufficient.'],
                ['title' => 'Accountability', 'icon' => 'clipboard-check', 'text' => 'All financial movements require admin approval and are logged with full audit trails for member visibility.'],
            ] as $item)
            <div class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-{{ $item['icon'] }} text-blue-600 text-sm"></i>
                    </div>
                    <h3 class="font-semibold text-gray-900">{{ $item['title'] }}</h3>
                </div>
                <p class="text-sm text-gray-500">{{ $item['text'] }}</p>
            </div>
            @endforeach
        </div>

        <h2 class="text-xl font-bold text-gray-900">Membership</h2>
        <p>Membership is by application only. We accept applications from individuals who are known to at least one existing member. All applications are reviewed by the admin team.</p>

        <h2 class="text-xl font-bold text-gray-900">Financial Structure</h2>
        <ul class="list-disc list-inside text-gray-600 space-y-2">
            <li>Members pay a fixed monthly fee set at the time of joining</li>
            <li>All payments are approved by admin before being counted in club funds</li>
            <li>Expenses require admin approval and are categorized clearly</li>
            <li>FDR investments are tracked with full transparency</li>
            <li>Annual reports are available to all members</li>
        </ul>
    </div>

    <div class="mt-10 text-center">
        <a href="{{ route('apply') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
            Apply for Membership
        </a>
    </div>
</div>
@endsection
