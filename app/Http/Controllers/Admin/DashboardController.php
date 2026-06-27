<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FdrRecord;
use App\Models\Income;
use App\Models\Member;
use App\Models\MemberAdditionalInfo;
use App\Models\MemberFamilyMember;
use App\Models\MembershipApplication;
use App\Models\MonthlyFeeSubmission;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear  = now()->year;

        $totalMembers        = Member::count();
        $activeMembers       = Member::where('status', 'active')->count();
        $pendingApplications = MembershipApplication::where('status', 'pending')->count();
        $pendingPayments     = MonthlyFeeSubmission::where('status', 'pending')->count();

        $expectedCollection = Member::where('status', 'active')->sum('monthly_fee_amount');
        $collectedThisMonth = MonthlyFeeSubmission::where('status', 'approved')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('amount');
        $dueThisMonth = max(0, $expectedCollection - $collectedThisMonth);

        $totalCollection = MonthlyFeeSubmission::where('status', 'approved')->sum('amount');
        $totalExpenses   = Expense::where('status', 'active')->sum('amount');
        $totalIncome     = Income::where('status', 'active')->sum('amount');
        $totalFdrPrincipal     = FdrRecord::whereIn('status', ['active', 'matured'])->sum('principal_amount');
        $totalFdrInterest      = FdrRecord::sum('interest_received');
        $activeFdrCount        = FdrRecord::where('status', 'active')->count();
        $closedFdrCount        = FdrRecord::whereIn('status', ['matured', 'closed', 'renewed'])->count();
        $thisMonthFdrInterest  = Income::where('income_type', 'fdr_interest')
            ->where('status', 'active')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
        $upcomingFdrMaturities = FdrRecord::where('status', 'active')
            ->where('maturity_date', '<=', now()->addDays(90))
            ->orderBy('maturity_date')
            ->limit(5)
            ->get();
        $netFund = $totalCollection + $totalIncome - $totalExpenses;

        $pendingPaymentsList = MonthlyFeeSubmission::with('member.user')
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        $recentApplications = MembershipApplication::where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        // Compute "days until next occurrence" for a date (birthday / anniversary)
        $daysUntil = function (\Carbon\Carbon $date) {
            $next = $date->copy()->setYear(now()->year);
            if ($next->lt(now()->startOfDay())) {
                $next->addYear();
            }
            return (int) $next->diffInDays(now()->startOfDay());
        };

        // Upcoming member birthdays (next 30 days)
        $memberBirthdays = Member::with('user')
            ->where('status', 'active')
            ->whereHas('user', fn($q) => $q->whereNotNull('date_of_birth'))
            ->get()
            ->map(function ($m) use ($daysUntil) {
                $m->_days = $daysUntil($m->user->date_of_birth);
                return $m;
            })
            ->filter(fn($m) => $m->_days <= 30)
            ->sortBy('_days')
            ->take(5)
            ->values();

        // Upcoming family birthdays (next 30 days)
        $familyBirthdays = MemberFamilyMember::with('member.user')
            ->whereHas('member', fn($q) => $q->where('status', 'active'))
            ->whereNotNull('date_of_birth')
            ->get()
            ->map(function ($f) use ($daysUntil) {
                $f->_days = $daysUntil($f->date_of_birth);
                return $f;
            })
            ->filter(fn($f) => $f->_days <= 30)
            ->sortBy('_days')
            ->take(5)
            ->values();

        // Upcoming marriage anniversaries (next 30 days)
        $upcomingAnniversaries = MemberAdditionalInfo::with('member.user')
            ->whereHas('member', fn($q) => $q->where('status', 'active'))
            ->whereNotNull('marriage_anniversary')
            ->get()
            ->map(function ($a) use ($daysUntil) {
                $a->_days = $daysUntil($a->marriage_anniversary);
                return $a;
            })
            ->filter(fn($a) => $a->_days <= 30)
            ->sortBy('_days')
            ->take(5)
            ->values();

        return view('admin.dashboard', compact(
            'totalMembers', 'activeMembers', 'pendingApplications', 'pendingPayments',
            'expectedCollection', 'collectedThisMonth', 'dueThisMonth',
            'totalCollection', 'totalExpenses', 'totalIncome',
            'totalFdrPrincipal', 'totalFdrInterest', 'netFund',
            'activeFdrCount', 'closedFdrCount', 'thisMonthFdrInterest', 'upcomingFdrMaturities',
            'pendingPaymentsList', 'recentApplications',
            'memberBirthdays', 'familyBirthdays', 'upcomingAnniversaries'
        ));
    }
}
