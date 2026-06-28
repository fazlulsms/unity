<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankDeposit;
use App\Models\BankWithdrawal;
use App\Models\Expense;
use App\Models\FdrRecord;
use App\Models\Income;
use App\Models\Member;
use App\Models\MemberAdditionalInfo;
use App\Models\MemberFamilyMember;
use App\Models\MembershipApplication;
use App\Models\MonthlyFeeSubmission;
use App\Support\DateRange;
use App\Support\FinanceSummary;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Dashboard defaults to the current month.
        $range = DateRange::fromRequest($request, 'this_month');
        $from  = $range->from;
        $to    = $range->to;

        $totalMembers        = Member::count();
        $activeMembers       = Member::where('status', 'active')->count();
        $pendingApplications = MembershipApplication::where('status', 'pending')->count();
        $pendingPayments     = MonthlyFeeSubmission::where('status', 'pending')->count();

        // ── Period-scoped finance figures ───────────────────────────────────
        $finance = FinanceSummary::all($from, $to);

        $expectedCollection = FinanceSummary::monthlyExpected($from, $to);
        $collectedThisMonth = $finance['monthly_collection'];
        $dueThisMonth       = max(0, $expectedCollection - $collectedThisMonth);

        $monthlyCollection = $finance['monthly_collection'];
        $boosterCollection = $finance['booster_collection'];
        $totalCollection   = $finance['total_member_contribution'];
        $totalExpenses     = $finance['total_expenses'];
        $totalIncome       = $finance['total_other_income'];
        $totalFdrPrincipal = $finance['total_active_fdr'];
        $totalFdrInterest  = $finance['total_fdr_interest'];
        $activeFdrCount    = $finance['fdr_created']['count'];
        $closedFdrCount    = $finance['fdr_closed']['count'];
        $thisMonthFdrInterest = $finance['total_fdr_interest'];
        $upcomingFdrMaturities = FdrRecord::where('status', 'active')
            ->where('maturity_date', '<=', now()->addDays(90))
            ->orderBy('maturity_date')
            ->limit(5)
            ->get();
        $netFund = $totalCollection + $totalIncome - $totalExpenses;

        // ── Bank & cash-flow summary (period scoped) ─────────────────────────
        $bankAccounts        = BankAccount::all();
        $totalBankDeposits   = $finance['total_bank_deposits'];
        $totalBankWithdrawn  = $finance['total_withdrawals'];
        $cashInHand          = $finance['cash_in_hand'];
        $totalBankAvailable  = $finance['total_available_balance'];
        $bankAccountsCount   = $bankAccounts->count();

        // ── Who hasn't paid (payment due list) ───────────────────────────────
        $dueList = $this->dueList($range);

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
            'range',
            'totalMembers', 'activeMembers', 'pendingApplications', 'pendingPayments',
            'expectedCollection', 'collectedThisMonth', 'dueThisMonth',
            'totalCollection', 'totalExpenses', 'totalIncome',
            'totalFdrPrincipal', 'totalFdrInterest', 'netFund',
            'activeFdrCount', 'closedFdrCount', 'thisMonthFdrInterest', 'upcomingFdrMaturities',
            'totalBankDeposits', 'totalBankWithdrawn', 'cashInHand', 'totalBankAvailable', 'bankAccountsCount',
            'monthlyCollection', 'boosterCollection',
            'dueList', 'recentApplications',
            'memberBirthdays', 'familyBirthdays', 'upcomingAnniversaries'
        ));
    }

    /**
     * Active members who have not fully paid for the selected period.
     */
    private function dueList(DateRange $range)
    {
        $members = Member::with('user')->where('status', 'active')->orderBy('id')->get();

        if ($range->isAll()) {
            return $members->map(fn($m) => [
                'member'   => $m,
                'expected' => $m->total_payable,
                'paid'     => $m->total_paid,
                'due'      => $m->total_due,
            ])->filter(fn($r) => $r['due'] > 0)->sortByDesc('due')->take(12)->values();
        }

        // Build the set of (year-month) keys covered by the range.
        $cursor    = $range->from->copy()->startOfMonth();
        $end       = $range->to->copy()->startOfMonth();
        $monthKeys = [];
        while ($cursor->lte($end)) {
            $monthKeys[] = $cursor->year . '-' . str_pad($cursor->month, 2, '0', STR_PAD_LEFT);
            $cursor->addMonthNoOverflow();
        }

        return $members->map(function ($m) use ($monthKeys) {
            $join = $m->join_date->copy()->startOfMonth();
            $payable = collect($monthKeys)->filter(function ($k) use ($join) {
                [$y, $mo] = explode('-', $k);
                return \Carbon\Carbon::create((int) $y, (int) $mo, 1)->gte($join);
            });
            $expected = $payable->count() * (float) $m->monthly_fee_amount;
            $paid = (float) $m->approvedFeeSubmissions()->get()
                ->filter(fn($s) => in_array($s->year . '-' . str_pad($s->month, 2, '0', STR_PAD_LEFT), $monthKeys, true))
                ->sum('amount');
            return [
                'member'   => $m,
                'expected' => $expected,
                'paid'     => $paid,
                'due'      => max(0.0, $expected - $paid),
            ];
        })->filter(fn($r) => $r['due'] > 0)->sortByDesc('due')->take(12)->values();
    }
}
