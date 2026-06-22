@extends('layouts.app')
@section('title', 'Due List')
@section('page-title', 'Due List')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

    {{-- Summary banner --}}
    <div class="card">
        <div class="card-body flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1">
                <p class="text-xs text-gray-400 font-medium">Total Outstanding (Active Members)</p>
                <p class="text-3xl font-bold text-red-600 mt-0.5">৳ {{ number_format($totalDue, 0) }}</p>
            </div>
            <div class="text-sm text-gray-400">
                {{ $members->count() }} member(s) with outstanding dues
            </div>
            <a href="{{ route('admin.collections.create') }}" class="btn btn-md btn-success shrink-0">
                <i class="fas fa-plus"></i> Add Payment
            </a>
        </div>
    </div>

    {{-- Due table --}}
    <div class="table-wrap">
        <div class="card-header">
            <p class="font-semibold text-gray-800 text-sm">Members with Outstanding Dues</p>
            <span class="text-xs text-gray-400">Sorted by highest due</span>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="th">Member</th>
                    <th class="th text-right">Expected</th>
                    <th class="th text-right">Paid</th>
                    <th class="th text-right text-red-600">Due</th>
                    <th class="th hidden md:table-cell">Last Payment</th>
                    <th class="th text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $row)
                @php $m = $row['member']; @endphp
                <tr class="tr">
                    <td class="td">
                        <div class="flex items-center gap-2">
                            <img src="{{ $m->user->photo_url }}"
                                 class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0" alt="">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $m->user->name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $m->member_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="td text-right text-gray-500 font-mono">৳ {{ number_format($row['expected'], 0) }}</td>
                    <td class="td text-right text-emerald-600 font-mono">৳ {{ number_format($row['paid'], 0) }}</td>
                    <td class="td text-right">
                        <span class="font-bold text-red-600 font-mono">৳ {{ number_format($row['due'], 0) }}</span>
                    </td>
                    <td class="td hidden md:table-cell text-gray-400 text-xs">
                        @if($row['last_payment'])
                            {{ date('F', mktime(0,0,0,$row['last_payment']->month,1)) }}
                            {{ $row['last_payment']->year }}
                            — {{ $row['last_payment']->payment_date->format('d M Y') }}
                        @else
                            <span class="italic">No payments yet</span>
                        @endif
                    </td>
                    <td class="td text-right">
                        <a href="{{ route('admin.collections.create', ['member' => $m->id]) }}"
                           class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> Pay
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="table-empty">
                        <i class="fas fa-check-circle text-emerald-400 text-2xl block mb-2"></i>
                        All active members are up to date.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($members->count() > 0)
            <tfoot>
                <tr class="bg-gray-50 border-t-2 border-gray-200">
                    <td class="th" colspan="2">Total</td>
                    <td class="th text-right text-emerald-700">৳ {{ number_format($members->sum('paid'), 0) }}</td>
                    <td class="th text-right text-red-600">৳ {{ number_format($totalDue, 0) }}</td>
                    <td class="th" colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <a href="{{ route('admin.collections.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to Collections
    </a>
</div>
@endsection
