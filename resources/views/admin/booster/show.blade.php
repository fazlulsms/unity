@extends('layouts.app')
@section('title', $booster->title)
@section('page-title', 'Booster Contribution')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5 max-w-screen-xl">

    {{-- Header --}}
    <div class="card p-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">{{ $booster->title }}</h2>
            <p class="text-sm text-gray-500">Period: {{ $booster->period_date->format('d M Y') }} · ৳{{ number_format($booster->expected_amount_per_member, 2) }} per member</p>
            <span class="badge-{{ $booster->isActive() ? 'active' : 'closed' }} mt-2 inline-block">{{ ucfirst($booster->status) }}</span>
            @if($booster->remarks)<p class="text-sm text-gray-600 mt-2">{{ $booster->remarks }}</p>@endif
        </div>
        <a href="{{ route('admin.booster.edit', $booster) }}" class="text-xs border border-gray-300 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-50">Edit Drive / Members</a>
    </div>

    {{-- Totals --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="card p-5"><p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Expected</p><p class="text-2xl font-bold text-gray-900 mt-1">৳{{ number_format($booster->total_expected, 0) }}</p></div>
        <div class="card p-5"><p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Collected</p><p class="text-2xl font-bold text-emerald-600 mt-1">৳{{ number_format($booster->total_deposited, 0) }}</p></div>
        <div class="card p-5"><p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Due</p><p class="text-2xl font-bold text-red-600 mt-1">৳{{ number_format($booster->total_due, 0) }}</p></div>
    </div>

    {{-- Record payment --}}
    @if($booster->isActive())
    <div class="card p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Record a Payment</h3>
        @if(session('error'))<div class="alert-error mb-3"><i class="fas fa-circle-exclamation text-red-500 mt-0.5 shrink-0"></i><span>{{ session('error') }}</span></div>@endif
        @php $inp = 'w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none'; @endphp
        <form action="{{ route('admin.booster.payment.store', $booster) }}" method="POST" enctype="multipart/form-data" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Member <span class="text-red-500">*</span></label>
                <select name="member_id" required class="{{ $inp }} cursor-pointer">
                    <option value="">Select member…</option>
                    @foreach($rows as $r)
                    <option value="{{ $r['member']->id }}">{{ $r['member']->user->name ?? 'Unknown' }} ({{ $r['member']->member_number }}) — Due ৳{{ number_format($r['due'], 0) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Payment Date <span class="text-red-500">*</span></label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="{{ $inp }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Amount (৳) <span class="text-red-500">*</span></label>
                <input type="number" name="amount" min="0.01" step="0.01" required class="{{ $inp }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                <select name="payment_method" class="{{ $inp }} cursor-pointer">
                    @foreach(['cash','bank','bkash','nagad','rocket','other'] as $pm)
                    <option value="{{ $pm }}">{{ ucfirst($pm) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Reference / Remarks</label>
                <input type="text" name="reference" class="{{ $inp }}">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Proof / Attachment</label>
                <input type="file" name="attachment" accept="image/jpeg,image/png,image/jpg,application/pdf" class="{{ $inp }} file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
            </div>
            <div class="lg:col-span-3">
                <button type="submit" class="bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-emerald-700">Record Payment</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Member-wise tracking --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
        <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-700">Member-wise Status</h3></div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Member</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Expected</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Deposited</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Due</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($rows as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-900">{{ $r['member']->user->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $r['member']->member_number }}</p>
                    </td>
                    <td class="px-5 py-3 text-right text-gray-700">৳{{ number_format($r['expected'], 0) }}</td>
                    <td class="px-5 py-3 text-right text-emerald-600">৳{{ number_format($r['deposited'], 0) }}</td>
                    <td class="px-5 py-3 text-right text-red-600">৳{{ number_format($r['due'], 0) }}</td>
                    <td class="px-5 py-3">
                        @php $c = ['paid'=>'approved','partial'=>'pending','due'=>'rejected'][$r['status']]; @endphp
                        <span class="badge-{{ $c }}">{{ ucfirst($r['status']) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No members assigned. <a href="{{ route('admin.booster.edit', $booster) }}" class="text-blue-600 hover:underline">Add members</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Payment history --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
        <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-700">Payment History</h3></div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Date</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Member</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500">Amount</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Method</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Reference</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Proof</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 text-gray-500">{{ $p->payment_date->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-gray-800">{{ $p->member->user->name ?? 'Unknown' }}</td>
                    <td class="px-5 py-3 text-right font-medium text-emerald-600">৳{{ number_format($p->amount, 2) }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ ucfirst($p->payment_method) }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $p->reference ?: '—' }}</td>
                    <td class="px-5 py-3">@if($p->attachment_url)<a href="{{ $p->attachment_url }}" target="_blank" class="text-blue-600 text-xs hover:underline"><i class="fas fa-paperclip"></i></a>@else<span class="text-gray-300">—</span>@endif</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No payments recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.booster.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Back to all drives</a>
</div>
@endsection
