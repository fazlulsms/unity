@extends('layouts.app')
@section('title', 'Statements')
@section('page-title', 'Statements & Downloads')
@section('sidebar') @include('partials.member-nav') @endsection

@section('content')
<div class="max-w-3xl space-y-5">

    <form method="GET" class="flex items-center gap-2">
        <label class="text-sm text-gray-600 font-medium">Statement year:</label>
        <select name="year" class="form-select w-auto" onchange="this.form.submit()">
            @foreach($availableYears as $y)
            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>

    <div class="grid sm:grid-cols-2 gap-5">
        {{-- Personal statement --}}
        <div class="card p-6 flex flex-col">
            <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center mb-3"><i class="fas fa-file-invoice text-blue-600"></i></div>
            <h2 class="text-base font-bold text-gray-900">Personal Member Statement</h2>
            <p class="text-sm text-gray-500 mt-1 flex-1">Your monthly fees, Booster Contribution, total contribution, dues and full payment history for {{ $year }}.</p>
            <div class="flex gap-2 mt-4">
                <a href="{{ route('member.statement', ['year' => $year]) }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2 rounded-lg text-sm hover:bg-gray-50">View</a>
                <a href="{{ route('member.statements.personal-pdf', ['year' => $year]) }}" class="flex-1 text-center bg-blue-600 text-white py-2 rounded-lg text-sm hover:bg-blue-700"><i class="fas fa-download"></i> PDF</a>
            </div>
        </div>

        {{-- Club finance statement --}}
        <div class="card p-6 flex flex-col">
            <div class="w-11 h-11 rounded-xl bg-violet-100 flex items-center justify-center mb-3"><i class="fas fa-building-columns text-violet-600"></i></div>
            <h2 class="text-base font-bold text-gray-900">Club Finance Statement</h2>
            <p class="text-sm text-gray-500 mt-1 flex-1">Club-wide position: member &amp; booster collection, bank deposits, cash in hand, FDR, withdrawals, income/expense and balance.</p>
            <div class="flex gap-2 mt-4">
                <a href="{{ route('member.statements.club-finance') }}" class="flex-1 text-center border border-gray-300 text-gray-700 py-2 rounded-lg text-sm hover:bg-gray-50">View</a>
                <a href="{{ route('member.statements.club-finance-pdf') }}" class="flex-1 text-center bg-violet-600 text-white py-2 rounded-lg text-sm hover:bg-violet-700"><i class="fas fa-download"></i> PDF</a>
            </div>
        </div>
    </div>
</div>
@endsection
