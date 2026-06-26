{{-- Overview --}}
<p class="sidebar-section">Overview</p>
<a href="{{ route('admin.dashboard') }}"
   class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fas fa-gauge-high w-4 text-center"></i>
    <span>Dashboard</span>
</a>

{{-- Members --}}
<p class="sidebar-section">Members</p>
<a href="{{ route('admin.applications.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
    <i class="fas fa-file-lines w-4 text-center"></i>
    <span>Applications</span>
    @php $pendingApps = \App\Models\MembershipApplication::where('status','pending')->count(); @endphp
    @if($pendingApps > 0)
    <span class="ml-auto text-xs bg-amber-500 text-white rounded-full w-5 h-5 flex items-center justify-center font-bold leading-none">
        {{ $pendingApps > 9 ? '9+' : $pendingApps }}
    </span>
    @endif
</a>
<a href="{{ route('admin.members.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
    <i class="fas fa-users w-4 text-center"></i>
    <span>Members</span>
</a>

{{-- Finance --}}
<p class="sidebar-section">Finance</p>
<a href="{{ route('admin.collections.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.collections.*') ? 'active' : '' }}">
    <i class="fas fa-bangladeshi-taka-sign w-4 text-center"></i>
    <span>Collections</span>
</a>
<a href="{{ route('admin.payments.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
    <i class="fas fa-circle-check w-4 text-center"></i>
    <span>Payment Approvals</span>
    @php $pendingPay = \App\Models\MonthlyFeeSubmission::where('status','pending')->count(); @endphp
    @if($pendingPay > 0)
    <span class="ml-auto text-xs bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center font-bold leading-none">
        {{ $pendingPay > 9 ? '9+' : $pendingPay }}
    </span>
    @endif
</a>
<a href="{{ route('admin.expenses.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
    <i class="fas fa-arrow-trend-down w-4 text-center"></i>
    <span>Expenses</span>
</a>
<a href="{{ route('admin.income.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.income.*') ? 'active' : '' }}">
    <i class="fas fa-arrow-trend-up w-4 text-center"></i>
    <span>Other Income</span>
</a>
<a href="{{ route('admin.fdr.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.fdr.*') ? 'active' : '' }}">
    <i class="fas fa-building-columns w-4 text-center"></i>
    <span>FDR Records</span>
</a>

{{-- Content --}}
<p class="sidebar-section">Content</p>
<a href="{{ route('admin.notices.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.notices.*') ? 'active' : '' }}">
    <i class="fas fa-bell w-4 text-center"></i>
    <span>Notices</span>
</a>
<a href="{{ route('admin.meeting-minutes.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.meeting-minutes.*') ? 'active' : '' }}">
    <i class="fas fa-clipboard-list w-4 text-center"></i>
    <span>Meeting Minutes</span>
</a>

{{-- System --}}
<p class="sidebar-section">System</p>
<a href="{{ route('admin.users.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
    <i class="fas fa-user-shield w-4 text-center"></i>
    <span>User Management</span>
</a>

{{-- Reports --}}
<p class="sidebar-section">Reports</p>
<a href="{{ route('admin.reports.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
    <i class="fas fa-chart-column w-4 text-center"></i>
    <span>Reports</span>
</a>

{{-- Public --}}
<div class="mt-2 pt-2 border-t border-slate-800">
    <a href="{{ route('home') }}" target="_blank"
       class="sidebar-link text-slate-500">
        <i class="fas fa-arrow-up-right-from-square w-4 text-center"></i>
        <span>Public Site</span>
    </a>
</div>
