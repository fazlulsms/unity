@extends('layouts.app')

@section('sidebar')
    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mb-2">Overview</div>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt w-4"></i> Dashboard
    </a>

    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mt-4 mb-2">Members</div>
    <a href="{{ route('admin.applications.index') }}" class="sidebar-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
        <i class="fas fa-file-alt w-4"></i> Applications
    </a>
    <a href="{{ route('admin.members.index') }}" class="sidebar-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
        <i class="fas fa-users w-4"></i> Members
    </a>

    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mt-4 mb-2">Finance</div>
    <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
        <i class="fas fa-check-circle w-4"></i> Payment Approvals
    </a>
    <a href="{{ route('admin.expenses.index') }}" class="sidebar-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
        <i class="fas fa-minus-circle w-4"></i> Expenses
    </a>
    <a href="{{ route('admin.income.index') }}" class="sidebar-link {{ request()->routeIs('admin.income.*') ? 'active' : '' }}">
        <i class="fas fa-plus-circle w-4"></i> Income
    </a>
    <a href="{{ route('admin.fdr.index') }}" class="sidebar-link {{ request()->routeIs('admin.fdr.*') ? 'active' : '' }}">
        <i class="fas fa-university w-4"></i> FDR Records
    </a>

    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mt-4 mb-2">Content</div>
    <a href="{{ route('admin.notices.index') }}" class="sidebar-link {{ request()->routeIs('admin.notices.*') ? 'active' : '' }}">
        <i class="fas fa-bell w-4"></i> Notices
    </a>
    <a href="{{ route('admin.meeting-minutes.index') }}" class="sidebar-link {{ request()->routeIs('admin.meeting-minutes.*') ? 'active' : '' }}">
        <i class="fas fa-clipboard w-4"></i> Meeting Minutes
    </a>

    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 mt-4 mb-2">Reports</div>
    <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <i class="fas fa-chart-bar w-4"></i> Reports
    </a>
    <div class="border-t border-gray-100 my-3"></div>
    <a href="{{ route('home') }}" class="sidebar-link">
        <i class="fas fa-globe w-4"></i> Public Site
    </a>
@endsection

@yield('admin-content')
