@extends('layouts.app')
@section('title', 'Member: ' . $member->user->name)
@section('page-title', 'Member Details')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="space-y-6">
    <div class="grid md:grid-cols-3 gap-6">
        {{-- Member info --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="text-center mb-5">
                <img src="{{ $member->user->photo_url }}" alt="Photo" class="w-20 h-20 rounded-full object-cover border-4 border-gray-100 mx-auto mb-3">
                <h2 class="font-bold text-gray-900 text-lg">{{ $member->user->name }}</h2>
                <p class="text-blue-600 font-mono text-sm">{{ $member->member_number }}</p>
                <span class="badge-{{ $member->status === 'active' ? 'active' : 'voided' }} mt-2 inline-block">{{ ucfirst($member->status) }}</span>
            </div>
            <div class="space-y-3 text-sm">
                @foreach([
                    ['Phone', $member->user->phone],
                    ['Email', $member->user->email],
                    ['Joined', $member->join_date->format('d M Y')],
                    ['Monthly Fee', '৳' . number_format($member->monthly_fee_amount, 2)],
                    ['Total Paid', '৳' . number_format($member->total_paid, 2)],
                    ['Due Amount', '৳' . number_format($member->total_due, 2)],
                ] as [$label, $value])
                <div class="flex justify-between">
                    <span class="text-gray-400">{{ $label }}</span>
                    <span class="text-gray-900 font-medium">{{ $value ?: '—' }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 flex gap-2">
                <a href="{{ route('admin.members.edit', $member) }}"
                    class="flex-1 text-center text-sm bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    Edit
                </a>
            </div>
        </div>

        {{-- Add manual payment --}}
        <div class="md:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Add Manual Payment</h3>
            <form action="{{ route('admin.members.payment', $member) }}" method="POST" class="grid sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Month</label>
                    <select name="month" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Year</label>
                    <select name="year" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        @for($y = now()->year - 1; $y <= now()->year; $y++)
                        <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Amount (৳)</label>
                    <input type="number" name="amount" value="{{ $member->monthly_fee_amount }}" required min="1"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Method</label>
                    <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach(['cash' => 'Cash', 'bank' => 'Bank', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket', 'other' => 'Other'] as $v => $l)
                        <option value="{{ $v }}">{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Reference</label>
                    <input type="text" name="transaction_reference"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-600 mb-1">Notes</label>
                    <input type="text" name="notes"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-medium hover:bg-blue-700 transition-colors text-sm">
                        Add & Approve Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Payment history --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Payment History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Period</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Amount</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Method</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($submissions as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium">{{ date('F', mktime(0,0,0,$s->month,1)) }} {{ $s->year }}</td>
                        <td class="px-5 py-3">৳{{ number_format($s->amount, 2) }}</td>
                        <td class="px-5 py-3 capitalize text-gray-500">{{ $s->payment_method }}</td>
                        <td class="px-5 py-3"><span class="badge-{{ $s->status }}">{{ ucfirst($s->status) }}</span></td>
                        <td class="px-5 py-3 text-gray-500">{{ $s->payment_date->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No payments.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($submissions->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $submissions->links() }}</div>
        @endif
    </div>

    <a href="{{ route('admin.members.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">← Back to members</a>
</div>
@endsection
