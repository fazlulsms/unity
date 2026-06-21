<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FdrRecord;
use App\Models\Income;
use App\Models\Member;
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
        $totalFdrPrincipal = FdrRecord::whereIn('status', ['active', 'matured'])->sum('principal_amount');
        $totalFdrInterest  = FdrRecord::sum('interest_received');
        $netFund         = $totalCollection + $totalIncome - $totalExpenses;

        $pendingPaymentsList = MonthlyFeeSubmission::with('member.user')
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        $recentApplications = MembershipApplication::where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalMembers', 'activeMembers', 'pendingApplications', 'pendingPayments',
            'expectedCollection', 'collectedThisMonth', 'dueThisMonth',
            'totalCollection', 'totalExpenses', 'totalIncome',
            'totalFdrPrincipal', 'totalFdrInterest', 'netFund',
            'pendingPaymentsList', 'recentApplications'
        ));
    }
}
