<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FdrRecord;
use App\Models\Income;
use App\Models\Member;
use App\Models\MonthlyFeeSubmission;
use App\Models\Notice;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $member = $user->member;

        $pendingPayments  = $member?->feeSubmissions()->where('status', 'pending')->count() ?? 0;
        $approvedPayments = $member?->feeSubmissions()->where('status', 'approved')->count() ?? 0;
        $totalPaid        = $member?->feeSubmissions()->where('status', 'approved')->sum('amount') ?? 0;

        $recentPayments = $member?->feeSubmissions()->with('receipt')->latest()->limit(5)->get() ?? collect();

        $notices = Notice::published()->latest('published_at')->limit(5)->get();

        $stats = $this->getTransparencyStats();

        return view('member.dashboard', compact(
            'member', 'pendingPayments', 'approvedPayments',
            'totalPaid', 'recentPayments', 'notices', 'stats'
        ));
    }

    public function transparency()
    {
        $stats = $this->getTransparencyStats();

        $recentTransactions = MonthlyFeeSubmission::with('member.user')
            ->where('status', 'approved')
            ->latest('approved_at')
            ->limit(15)
            ->get();

        $fdrSummary = FdrRecord::select('bank_name', 'status', 'principal_amount', 'opening_date', 'maturity_date', 'interest_rate')
            ->get();

        $monthlyExpenses = Expense::where('status', 'active')
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit(12)
            ->get();

        $notices = Notice::published()->latest('published_at')->limit(5)->get();

        return view('member.transparency', compact('stats', 'recentTransactions', 'fdrSummary', 'monthlyExpenses', 'notices'));
    }

    private function getTransparencyStats(): array
    {
        $totalMembers    = Member::count();
        $activeMembers   = Member::where('status', 'active')->count();
        $totalCollection = MonthlyFeeSubmission::where('status', 'approved')->sum('amount');
        $totalExpense    = Expense::where('status', 'active')->sum('amount');
        $totalIncome     = Income::where('status', 'active')->sum('amount');
        $totalFdr        = FdrRecord::whereIn('status', ['active', 'matured'])->sum('principal_amount');
        $fundBalance     = $totalCollection + $totalIncome - $totalExpense;

        $currentMonth = now()->month;
        $currentYear  = now()->year;
        $expected     = Member::where('status', 'active')->sum('monthly_fee_amount');
        $collected    = MonthlyFeeSubmission::where('status', 'approved')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('amount');
        $collectionPercent = $expected > 0 ? round(($collected / $expected) * 100, 1) : 0;

        return compact(
            'totalMembers', 'activeMembers', 'totalCollection', 'totalExpense',
            'totalIncome', 'totalFdr', 'fundBalance', 'expected', 'collected', 'collectionPercent'
        );
    }
}
