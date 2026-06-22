@extends('layouts.app')
@section('title', 'FDR Records')
@section('page-title', 'FDR Management')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div class="text-sm text-gray-500">
            Total Principal: <strong>৳{{ number_format($totalPrincipal, 2) }}</strong> ·
            Interest Earned: <strong class="text-green-600">৳{{ number_format($totalInterest, 2) }}</strong>
        </div>
        <a href="{{ route('admin.fdr.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">+ Add FDR</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Bank</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">FDR No.</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Principal</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Rate</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Open Date</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Maturity</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($fdrs as $fdr)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-900">{{ $fdr->bank_name }}</p>
                        <p class="text-xs text-gray-400">{{ $fdr->branch }}</p>
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $fdr->fdr_number }}</td>
                    <td class="px-5 py-3 font-semibold">৳{{ number_format($fdr->principal_amount, 2) }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $fdr->interest_rate }}%</td>
                    <td class="px-5 py-3 text-gray-500">{{ $fdr->opening_date->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $fdr->maturity_date->format('d M Y') }}</td>
                    <td class="px-5 py-3">
                        @php $c = ['active'=>'active','matured'=>'approved','renewed'=>'pending','closed'=>'voided'][$fdr->status] ?? 'voided'; @endphp
                        <span class="badge-{{ $c }}">{{ ucfirst($fdr->status) }}</span>
                    </td>
                    <td class="px-5 py-3 flex gap-2">
                        <a href="{{ route('admin.fdr.show', $fdr) }}" class="text-gray-500 text-xs hover:underline">View</a>
                        <a href="{{ route('admin.fdr.edit', $fdr) }}" class="text-blue-600 text-xs hover:underline">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400">No FDR records.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($fdrs->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $fdrs->links() }}</div>
        @endif
    </div>
</div>
@endsection
