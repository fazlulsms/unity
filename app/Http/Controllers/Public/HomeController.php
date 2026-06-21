<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FdrRecord;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Member;
use App\Models\MonthlyFeeSubmission;
use App\Models\Notice;

class HomeController extends Controller
{
    public function index()
    {
        $stats = $this->getPublicStats();
        $notices = Notice::published()->public()->latest('published_at')->limit(3)->get();
        return view('public.home', compact('stats', 'notices'));
    }

    public function about()
    {
        return view('public.about');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function events()
    {
        return view('public.events');
    }

    public function notices()
    {
        $notices = Notice::published()->public()->latest('published_at')->paginate(10);
        return view('public.notices', compact('notices'));
    }

    public function transparency()
    {
        $stats = $this->getPublicStats();

        $recentTransactions = MonthlyFeeSubmission::with('member.user')
            ->where('status', 'approved')
            ->latest('approved_at')
            ->limit(10)
            ->get();

        $fdrSummary = FdrRecord::select('bank_name', 'status', 'principal_amount', 'opening_date', 'maturity_date', 'interest_rate')
            ->where('status', 'active')
            ->get();

        $monthlyExpenses = Expense::where('status', 'active')
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total, category')
            ->groupBy('year', 'month', 'category')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit(24)
            ->get();

        $notices = Notice::published()->public()->latest('published_at')->limit(5)->get();

        return view('public.transparency', compact('stats', 'recentTransactions', 'fdrSummary', 'monthlyExpenses', 'notices'));
    }

    private function getPublicStats(): array
    {
        $totalMembers   = Member::count();
        $activeMembers  = Member::where('status', 'active')->count();
        $totalCollection = MonthlyFeeSubmission::where('status', 'approved')->sum('amount');
        $totalExpense   = Expense::where('status', 'active')->sum('amount');
        $totalIncome    = Income::where('status', 'active')->sum('amount');
        $totalFdr       = FdrRecord::whereIn('status', ['active', 'matured'])->sum('principal_amount');
        $fundBalance    = $totalCollection + $totalIncome - $totalExpense;

        $currentMonth = now()->month;
        $currentYear  = now()->year;
        $expectedCollection = Member::where('status', 'active')->sum('monthly_fee_amount');
        $collectedThisMonth = MonthlyFeeSubmission::where('status', 'approved')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('amount');

        $collectionPercent = $expectedCollection > 0
            ? round(($collectedThisMonth / $expectedCollection) * 100, 1)
            : 0;

        return compact(
            'totalMembers', 'activeMembers', 'totalCollection', 'totalExpense',
            'totalIncome', 'totalFdr', 'fundBalance', 'expectedCollection',
            'collectedThisMonth', 'collectionPercent'
        );
    }
}
