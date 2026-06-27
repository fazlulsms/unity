@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports')
@section('sidebar') @include('partials.admin-nav') @endsection

@section('content')
<div class="grid sm:grid-cols-2 md:grid-cols-3 gap-5">
    @foreach([
        ['title' => 'Member List',           'desc' => 'All active members with details',            'icon' => 'users',       'color' => 'blue',   'route' => 'admin.reports.members'],
        ['title' => 'Monthly Collections',   'desc' => 'Payment collection report by year/month',    'icon' => 'money-bill',  'color' => 'green',  'route' => 'admin.reports.collections'],
        ['title' => 'Due Report',            'desc' => 'Members with outstanding dues',               'icon' => 'exclamation', 'color' => 'red',    'route' => 'admin.reports.dues'],
        ['title' => 'Expense Report',        'desc' => 'All expenses by year',                       'icon' => 'minus-circle','color' => 'orange', 'route' => 'admin.reports.expenses'],
        ['title' => 'Annual Fund Summary',   'desc' => 'Yearly income, expenses, and balance',       'icon' => 'chart-bar',   'color' => 'purple', 'route' => 'admin.reports.annual'],
        ['title' => 'Occasions & Reminders','desc' => 'Birthdays, anniversaries & member religion',  'icon' => 'birthday-cake','color' => 'pink',   'route' => 'admin.reports.occasions'],
    ] as $r)
    <a href="{{ route($r['route']) }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow group">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-{{ $r['color'] }}-100 rounded-xl flex items-center justify-center group-hover:bg-{{ $r['color'] }}-200 transition-colors">
                <i class="fas fa-{{ $r['icon'] }} text-{{ $r['color'] }}-600 text-lg"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">{{ $r['title'] }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $r['desc'] }}</p>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endsection
