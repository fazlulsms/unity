<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\FdrRecord;
use App\Models\Income;
use App\Models\Member;
use App\Models\MonthlyFeeSubmission;
use App\Models\Notice;
use App\Support\DateRange;
use App\Support\FinanceSummary;
use App\Support\MemberStatement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $member = $user->member;

        // Dashboard defaults to the current month.
        $range = DateRange::fromRequest($request, 'this_month');

        $pendingPayments  = $member?->feeSubmissions()->where('status', 'pending')->count() ?? 0;
        $approvedPayments = $member?->feeSubmissions()->where('status', 'approved')->count() ?? 0;
        $totalPaid        = $member?->feeSubmissions()->where('status', 'approved')->sum('amount') ?? 0;

        // "Who hasn't paid" for this member = their own unpaid months in the period.
        $dueRows = collect();
        if ($member) {
            $statement = MemberStatement::personal($member, $range);
            $dueRows = collect($statement['rows'])->filter(fn($r) => $r['due'] > 0)->values();
        }

        $notices = Notice::published()->latest('published_at')->limit(5)->get();

        $stats = $this->getTransparencyStats();

        // Read-only club financial position (period scoped, auto-updates with admin entries).
        $finance      = FinanceSummary::all($range->from, $range->to);
        $bankAccounts = BankAccount::orderBy('bank_name')->get();

        return view('member.dashboard', compact(
            'member', 'range', 'pendingPayments', 'approvedPayments',
            'totalPaid', 'dueRows', 'notices', 'stats',
            'finance', 'bankAccounts'
        ));
    }

    public function notices()
    {
        $notices = Notice::published()->latest('published_at')->paginate(15);
        return view('member.notices', compact('notices'));
    }

    public function transparency(Request $request)
    {
        // Mirrors the admin dashboard's financial information — read-only.
        $range = DateRange::fromRequest($request, 'this_month');
        $from  = $range->from;
        $to    = $range->to;
        $asOf  = $range->asOf();

        $finance = FinanceSummary::all($from, $to);

        $totalMembers  = Member::count();
        $activeMembers = Member::where('status', 'active')->count();

        $expected          = FinanceSummary::monthlyExpected($from, $to);
        $collected         = $finance['monthly_collection'];
        $due               = max(0.0, $expected - $collected);
        $collectionPercent = $expected > 0 ? round($collected / $expected * 100, 1) : 0;
        $netFund = $finance['total_member_contribution'] + $finance['total_other_income'] - $finance['total_expenses'];

        // Bank-wise figures: flows in period, balances/active FDR as of period end.
        $accounts = BankAccount::orderBy('bank_name')->get();
        $bankRows = $accounts->map(fn($a) => [
            'account'   => $a,
            'deposited' => $a->depositsBetween($from, $to),
            'available' => $a->availableBalanceAsOf($asOf),
            'activeFdr' => $a->activeFdrAsOf($asOf),
            'interest'  => $a->fdrInterestBetween($from, $to),
            'withdrawn' => $a->withdrawalsBetween($from, $to),
        ]);

        $recentTransactions = MonthlyFeeSubmission::with('member.user')
            ->where('status', 'approved')
            ->when($from, fn($q) => $q->whereDate('payment_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('payment_date', '<=', $to))
            ->latest('payment_date')->latest('id')
            ->limit(12)
            ->get();

        $fdrSummary = FdrRecord::orderByDesc('opening_date')->limit(8)->get();

        return view('member.transparency', compact(
            'range', 'finance', 'totalMembers', 'activeMembers',
            'expected', 'collected', 'due', 'collectionPercent', 'netFund',
            'bankRows', 'recentTransactions', 'fdrSummary'
        ));
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
