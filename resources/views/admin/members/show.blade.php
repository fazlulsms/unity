@extends('layouts.app')
@section('title', 'Member: ' . $member->user->name)
@section('page-title', 'Member Profile')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="max-w-5xl space-y-5">

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    {{-- Profile header --}}
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row items-start gap-5">
                {{-- Photo --}}
                <img src="{{ $member->user->photo_url }}"
                     class="w-24 h-24 rounded-xl object-cover border border-gray-200 shrink-0" alt="">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-3 mb-1">
                        <h2 class="text-xl font-bold text-gray-900">{{ $member->user->name }}</h2>
                        <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $member->member_number }}</span>
                        <span class="badge-{{ $member->status }}">{{ ucfirst($member->status) }}</span>
                    </div>
                    <p class="text-gray-500 text-sm">{{ $member->user->phone ?? '—' }} · {{ $member->user->email ?? '—' }}</p>
                    <p class="text-gray-400 text-xs mt-1">Member since {{ $member->join_date->format('d M Y') }}</p>
                    @if($member->user->email_verified_at)
                        <p class="text-xs text-emerald-600 mt-0.5"><i class="fas fa-check-circle"></i> Email verified</p>
                    @else
                        <p class="text-xs text-amber-600 mt-0.5"><i class="fas fa-exclamation-circle"></i> Email not verified</p>
                    @endif
                </div>
                {{-- Action buttons --}}
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-pen"></i> Edit
                    </a>
                    <a href="{{ route('admin.members.statement', $member) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-file-alt"></i> Statement
                    </a>
                    <a href="{{ route('admin.members.profile-pdf', $member) }}" class="btn btn-sm btn-secondary" target="_blank">
                        <i class="fas fa-file-pdf"></i> Profile PDF
                    </a>
                    @if($member->isActive())
                    <form action="{{ route('admin.members.deactivate', $member) }}" method="POST"
                          onsubmit="return confirm('Deactivate this member?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">
                            <i class="fas fa-pause-circle"></i> Deactivate
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.members.reactivate', $member) }}" method="POST"
                          onsubmit="return confirm('Reactivate this member?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fas fa-play-circle"></i> Reactivate
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-5">

        {{-- Full profile details --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="card">
                <div class="card-header"><p class="font-semibold text-gray-800 text-sm">Personal Information</p></div>
                <div class="card-body grid sm:grid-cols-2 gap-x-8 gap-y-4">
                    @foreach([
                        'Address'           => $member->user->address ?: '—',
                        'Date of Birth'     => $member->user->date_of_birth?->format('d M Y') ?? '—',
                        'Profession'        => $member->user->profession ?: '—',
                        'Emergency Contact' => $member->user->emergency_contact ?: '—',
                        'Nominee Name'      => $member->user->nominee_name ?: '—',
                        'Nominee Contact'   => $member->user->nominee_contact ?: '—',
                    ] as $label => $value)
                    <div>
                        <p class="text-xs text-gray-400 font-medium">{{ $label }}</p>
                        <p class="text-sm text-gray-800 mt-0.5">{{ $value }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Membership & financial summary --}}
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="stat-card text-center">
                    <p class="text-xs text-gray-400 font-medium mb-1">Monthly Fee</p>
                    <p class="text-xl font-bold text-gray-900">৳ {{ number_format($member->monthly_fee_amount, 0) }}</p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-xs text-gray-400 font-medium mb-1">Total Paid</p>
                    <p class="text-xl font-bold text-emerald-600">৳ {{ number_format($member->total_paid, 0) }}</p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-xs text-gray-400 font-medium mb-1">Total Due</p>
                    <p class="text-xl font-bold {{ $member->total_due > 0 ? 'text-red-600' : 'text-gray-400' }}">৳ {{ number_format($member->total_due, 0) }}</p>
                </div>
            </div>

            @if($lastPayment)
            <div class="card">
                <div class="card-body flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fas fa-check text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium">Last Payment</p>
                        <p class="text-sm font-semibold text-gray-800">
                            ৳ {{ number_format($lastPayment->amount, 2) }} —
                            {{ date('F', mktime(0,0,0,$lastPayment->month,1)) }} {{ $lastPayment->year }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $lastPayment->payment_date->format('d M Y') }} · {{ ucfirst($lastPayment->payment_method) }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Payment history --}}
            <div class="table-wrap">
                <div class="card-header">
                    <p class="font-semibold text-gray-800 text-sm">Payment History</p>
                    <a href="{{ route('admin.members.statement', $member) }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-file-alt"></i> Full Statement
                    </a>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="th">Period</th>
                            <th class="th">Amount</th>
                            <th class="th hidden sm:table-cell">Method</th>
                            <th class="th">Status</th>
                            <th class="th hidden md:table-cell">Date</th>
                            <th class="th text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $sub)
                        <tr class="tr">
                            <td class="td font-medium">{{ date('F', mktime(0,0,0,$sub->month,1)) }} {{ $sub->year }}</td>
                            <td class="td">৳ {{ number_format($sub->amount, 2) }}</td>
                            <td class="td hidden sm:table-cell text-gray-500 capitalize">{{ $sub->payment_method }}</td>
                            <td class="td">
                                <span class="badge-{{ $sub->status }}">{{ ucfirst($sub->status) }}</span>
                            </td>
                            <td class="td hidden md:table-cell text-gray-500 text-xs">{{ $sub->payment_date->format('d M Y') }}</td>
                            <td class="td text-right">
                                @if($sub->receipt)
                                <a href="{{ route('member.receipts.download', $sub->receipt) }}"
                                   class="btn btn-sm btn-ghost text-xs" target="_blank">
                                    <i class="fas fa-download"></i>
                                </a>
                                @else
                                <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="table-empty">No payment records.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($submissions->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $submissions->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Side: Add payment form --}}
        <div class="space-y-5">
            <div class="card">
                <div class="card-header"><p class="font-semibold text-gray-800 text-sm">Add Manual Payment</p></div>
                <div class="card-body">
                    <form action="{{ route('admin.members.payment', $member) }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Month</label>
                                <select name="month" class="form-select">
                                    @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ date('M', mktime(0,0,0,$m,1)) }}
                                    </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Year</label>
                                <select name="year" class="form-select">
                                    @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Amount (৳)</label>
                            <input type="number" name="amount" value="{{ $member->monthly_fee_amount }}"
                                   required min="1" step="0.01" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}"
                                   required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Method</label>
                            <select name="payment_method" class="form-select">
                                @foreach(['cash','bank','bkash','nagad','rocket','other'] as $m)
                                <option value="{{ $m }}">{{ ucfirst($m) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Reference (optional)</label>
                            <input type="text" name="transaction_reference" class="form-input" placeholder="Txn ID / ref">
                        </div>
                        <div>
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="notes" rows="2" class="form-textarea"></textarea>
                        </div>
                        <button type="submit" class="btn-success w-full">
                            <i class="fas fa-plus"></i> Add Payment
                        </button>
                    </form>
                </div>
            </div>

            @if($member->application)
            <div class="card">
                <div class="card-body">
                    <p class="text-xs text-gray-400 font-medium mb-2">Linked Application</p>
                    <a href="{{ route('admin.applications.show', $member->application) }}"
                       class="text-sm text-blue-600 hover:underline">
                        View original application →
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.members.index') }}" class="inline-block text-sm text-gray-500 hover:text-gray-700">
        ← Back to members
    </a>
</div>
@endsection
