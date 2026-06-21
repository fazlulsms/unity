@extends('layouts.app')
@section('title', 'Receipt #{{ $receipt->receipt_number }}')
@section('page-title', 'Receipt')

@section('sidebar')
    <a href="{{ route('member.fees.index') }}" class="sidebar-link active">
        <i class="fas fa-money-bill w-4"></i> My Payments
    </a>
@endsection

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="bg-blue-700 text-white px-6 py-8 text-center">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-receipt text-white text-xl"></i>
            </div>
            <h1 class="text-xl font-bold">Unity Club</h1>
            <p class="text-blue-200 text-sm mt-1">Official Payment Receipt</p>
        </div>

        <div class="px-6 py-6 space-y-4">
            <div class="text-center border-b border-dashed border-gray-200 pb-4">
                <p class="text-xs text-gray-400">Receipt Number</p>
                <p class="text-lg font-bold text-gray-900">{{ $receipt->receipt_number }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['Member Name', $receipt->member_name],
                    ['Period', $receipt->month_name . ' ' . $receipt->year],
                    ['Amount', '৳' . number_format($receipt->amount, 2)],
                    ['Payment Method', ucfirst($receipt->payment_method)],
                    ['Payment Date', $receipt->payment_date->format('d M Y')],
                    ['Approved Date', $receipt->approved_date->format('d M Y')],
                    ['Authorized By', $receipt->authorized_by],
                ] as [$label, $value])
                <div>
                    <p class="text-xs text-gray-400">{{ $label }}</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $value }}</p>
                </div>
                @endforeach
            </div>

            <div class="bg-gray-50 rounded-lg p-3 text-center mt-4">
                <p class="text-xs text-gray-400">This is a computer-generated receipt. No signature required.</p>
            </div>
        </div>

        <div class="px-6 pb-6">
            <button onclick="window.print()" class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm">
                <i class="fas fa-print mr-2"></i> Print Receipt
            </button>
        </div>
    </div>
</div>
@endsection
