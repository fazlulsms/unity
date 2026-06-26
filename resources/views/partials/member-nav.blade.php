<p class="sidebar-section">My Portal</p>
<a href="{{ route('member.dashboard') }}"
   class="sidebar-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
    <i class="fas fa-house w-4 text-center"></i>
    <span>Dashboard</span>
</a>
<a href="{{ route('member.profile') }}"
   class="sidebar-link {{ request()->routeIs('member.profile') ? 'active' : '' }}">
    <i class="fas fa-user w-4 text-center"></i>
    <span>My Profile</span>
</a>
<a href="{{ route('member.fees.index') }}"
   class="sidebar-link {{ request()->routeIs('member.fees.*') ? 'active' : '' }}">
    <i class="fas fa-money-bill-wave w-4 text-center"></i>
    <span>My Payments</span>
</a>
<a href="{{ route('member.notices') }}"
   class="sidebar-link {{ request()->routeIs('member.notices') ? 'active' : '' }}">
    <i class="fas fa-bell w-4 text-center"></i>
    <span>Notices</span>
</a>
<a href="{{ route('member.transparency') }}"
   class="sidebar-link {{ request()->routeIs('member.transparency') ? 'active' : '' }}">
    <i class="fas fa-chart-pie w-4 text-center"></i>
    <span>Club Finances</span>
</a>
<div class="mt-2 pt-2 border-t border-slate-800">
    <a href="{{ route('home') }}" target="_blank" class="sidebar-link text-slate-500">
        <i class="fas fa-arrow-up-right-from-square w-4 text-center"></i>
        <span>Public Site</span>
    </a>
</div>
