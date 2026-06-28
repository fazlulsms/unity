@extends('layouts.app')
@section('title', 'My Payments')
@section('page-title', 'My Payments')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="max-w-screen-lg space-y-5">

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-5 border-t-4 border-emerald-500">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Monthly Paid</p>
            <p class="text-2xl font-bold text-emerald-600">৳{{ number_format($totalPaid, 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Lifetime approved fees</p>
        </div>
        <div class="card p-5 border-t-4 border-amber-400">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Booster Paid</p>
            <p class="text-2xl font-bold text-amber-600">৳{{ number_format($boosterPaid, 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $boosterDue > 0 ? '৳' . number_format($boosterDue, 0) . ' due' : 'All booster paid' }}</p>
        </div>
        <div class="card p-5 border-t-4 border-red-400">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Monthly Due</p>
            <p class="text-2xl font-bold text-red-600">৳{{ number_format($totalDue, 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Outstanding balance</p>
        </div>
        <div class="card p-5 border-t-4 border-blue-500">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Monthly Fee</p>
            <p class="text-2xl font-bold text-blue-600">৳{{ number_format($member->monthly_fee_amount, 0) }}</p>
            <p class="text-xs text-gray-400 mt-1">Per month</p>
        </div>
    </div>

    {{-- Payment table --}}
    <div class="table-wrap">
        <div class="card-header">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Payment History</h2>
                <p class="text-xs text-gray-400 mt-0.5">All your fee submissions</p>
            </div>
            <a href="{{ route('member.fees.create') }}" class="btn-primary btn-sm shrink-0">
                <i class="fas fa-plus"></i> Submit Payment
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="th">Period</th>
                        <th class="th">Amount</th>
                        <th class="th hidden sm:table-cell">Date</th>
                        <th class="th hidden md:table-cell">Method</th>
                        <th class="th">Status</th>
                        <th class="th text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $s)
                    <tr class="tr">
                        <td class="td font-semibold text-gray-900">
                            {{ date('F', mktime(0,0,0,$s->month,1)) }} {{ $s->year }}
                        </td>
                        <td class="td font-bold">৳{{ number_format($s->amount, 0) }}</td>
                        <td class="td hidden sm:table-cell text-gray-500">{{ $s->payment_date->format('d M Y') }}</td>
                        <td class="td hidden md:table-cell text-gray-500 capitalize">{{ $s->payment_method }}</td>
                        <td class="td">
                            <span class="badge-{{ $s->status }}">{{ ucfirst($s->status) }}</span>
                        </td>
                        <td class="td text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('member.fees.show', $s) }}" class="btn-ghost btn-xs">View</a>
                                @if($s->status === 'approved' && $s->receipt)
                                <a href="{{ route('member.receipts.download', $s->receipt) }}"
                                   class="btn-success btn-xs">
                                    <i class="fas fa-download"></i> Receipt
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="table-empty">
                            <i class="fas fa-receipt text-3xl text-gray-200 mb-2 block"></i>
                            No payments submitted yet.
                            <div class="mt-3">
                                <a href="{{ route('member.fees.create') }}" class="btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Submit First Payment
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
        <div class="px-5 py-4 border-t border-gray-50">{{ $submissions->links() }}</div>
        @endif
    </div>

    {{-- Booster Contribution transactions --}}
    <div class="table-wrap">
        <div class="card-header">
            <div>
                <h2 class="text-sm font-semibold text-gray-900"><i class="fas fa-bolt text-amber-500"></i> Booster Contributions</h2>
                <p class="text-xs text-gray-400 mt-0.5">Special contributions outside the monthly fee</p>
            </div>
            <a href="{{ route('member.statement') }}" class="btn-ghost btn-sm shrink-0 text-blue-600">Full statement</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="th">Drive</th>
                        <th class="th">Amount</th>
                        <th class="th hidden sm:table-cell">Date</th>
                        <th class="th hidden md:table-cell">Method</th>
                        <th class="th hidden lg:table-cell">Reference</th>
                        <th class="th text-right">Proof</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boosterPayments as $b)
                    <tr class="tr">
                        <td class="td font-semibold text-gray-900">{{ $b->boosterContribution->title ?? 'Booster' }}</td>
                        <td class="td font-bold text-amber-600">৳{{ number_format($b->amount, 0) }}</td>
                        <td class="td hidden sm:table-cell text-gray-500">{{ $b->payment_date->format('d M Y') }}</td>
                        <td class="td hidden md:table-cell text-gray-500 capitalize">{{ $b->payment_method }}</td>
                        <td class="td hidden lg:table-cell text-gray-500">{{ $b->reference ?: '—' }}</td>
                        <td class="td text-right">
                            @if($b->attachment_url)
                            <a href="{{ $b->attachment_url }}" target="_blank" class="btn-ghost btn-xs text-blue-600"><i class="fas fa-paperclip"></i> View</a>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="table-empty">
                            <i class="fas fa-bolt text-3xl text-gray-200 mb-2 block"></i>
                            No booster contributions yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
