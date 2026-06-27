<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FdrRecord;
use App\Models\Income;
use App\Models\Member;
use App\Models\MonthlyFeeSubmission;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function members(Request $request)
    {
        $members = Member::with('user')->where('status', 'active')->get();

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.members-pdf', compact('members'));
            return $pdf->download('members-report.pdf');
        }

        return view('admin.reports.members', compact('members'));
    }

    public function collections(Request $request)
    {
        $year  = $request->year ?? now()->year;
        $month = $request->month ?? null;

        $query = MonthlyFeeSubmission::with('member.user')
            ->where('status', 'approved')
            ->where('year', $year);

        if ($month) {
            $query->where('month', $month);
        }

        $collections = $query->orderBy('year')->orderBy('month')->orderBy('member_id')->get();
        $total = $collections->sum('amount');

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.collections-pdf', compact('collections', 'total', 'year', 'month'));
            return $pdf->download("collections-{$year}.pdf");
        }

        return view('admin.reports.collections', compact('collections', 'total', 'year', 'month'));
    }

    public function dues(Request $request)
    {
        $members = Member::with(['user', 'feeSubmissions' => fn($q) => $q->where('status', 'approved')])
            ->where('status', 'active')
            ->get()
            ->map(function ($member) {
                $paid     = (float) $member->feeSubmissions->sum('amount');
                $expected = $member->total_payable;
                $due      = max(0.0, $expected - $paid);
                return array_merge($member->toArray(), [
                    'user_name'      => $member->user->name,
                    'expected_total' => $expected,
                    'paid_total'     => $paid,
                    'due_amount'     => $due,
                ]);
            })
            ->filter(fn($m) => $m['due_amount'] > 0);

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.dues-pdf', compact('members'));
            return $pdf->download('dues-report.pdf');
        }

        return view('admin.reports.dues', compact('members'));
    }

    public function expenses(Request $request)
    {
        $year  = $request->year ?? now()->year;

        $expenses = Expense::where('status', 'active')
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        $total     = $expenses->sum('amount');
        $byCategory = $expenses->groupBy('category')->map->sum('amount');

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.expenses-pdf', compact('expenses', 'total', 'byCategory', 'year'));
            return $pdf->download("expenses-{$year}.pdf");
        }

        return view('admin.reports.expenses', compact('expenses', 'total', 'byCategory', 'year'));
    }

    public function occasions(Request $request)
    {
        $filter  = $request->get('filter', 'member_birthdays');
        $days    = (int) $request->get('days', 365);
        $results = collect();

        $daysUntil = function (\Carbon\Carbon $date) {
            $next = $date->copy()->setYear(now()->year);
            if ($next->lt(now()->startOfDay())) {
                $next->addYear();
            }
            return (int) $next->diffInDays(now()->startOfDay());
        };

        if ($filter === 'member_birthdays') {
            $results = \App\Models\Member::with('user')
                ->where('status', 'active')
                ->whereHas('user', fn($q) => $q->whereNotNull('date_of_birth'))
                ->get()
                ->map(fn($m) => (object)[
                    'label' => $m->user->name,
                    'sub'   => $m->member_number,
                    'date'  => $m->user->date_of_birth,
                    'days'  => $daysUntil($m->user->date_of_birth),
                    'link'  => route('admin.members.show', $m),
                    'photo' => $m->user->photo_url,
                ])
                ->filter(fn($r) => $r->days <= $days)
                ->sortBy('days')
                ->values();
        }

        if ($filter === 'family_birthdays') {
            $results = \App\Models\MemberFamilyMember::with('member.user')
                ->whereHas('member', fn($q) => $q->where('status', 'active'))
                ->whereNotNull('date_of_birth')
                ->get()
                ->map(fn($f) => (object)[
                    'label' => $f->name,
                    'sub'   => $f->relationship_label . ' of ' . $f->member->user->name,
                    'date'  => $f->date_of_birth,
                    'days'  => $daysUntil($f->date_of_birth),
                    'link'  => route('admin.members.additional-info.show', $f->member),
                    'photo' => $f->photo_url,
                ])
                ->filter(fn($r) => $r->days <= $days)
                ->sortBy('days')
                ->values();
        }

        if ($filter === 'anniversaries') {
            $results = \App\Models\MemberAdditionalInfo::with('member.user')
                ->whereHas('member', fn($q) => $q->where('status', 'active'))
                ->whereNotNull('marriage_anniversary')
                ->get()
                ->map(fn($a) => (object)[
                    'label' => $a->member->user->name,
                    'sub'   => 'Anniversary on ' . $a->marriage_anniversary->format('d M'),
                    'date'  => $a->marriage_anniversary,
                    'days'  => $daysUntil($a->marriage_anniversary),
                    'link'  => route('admin.members.additional-info.show', $a->member),
                    'photo' => $a->member->user->photo_url,
                ])
                ->filter(fn($r) => $r->days <= $days)
                ->sortBy('days')
                ->values();
        }

        if ($filter === 'religion') {
            $results = \App\Models\MemberAdditionalInfo::with('member.user')
                ->whereHas('member', fn($q) => $q->where('status', 'active'))
                ->whereNotNull('religion')
                ->get()
                ->groupBy('religion')
                ->map(fn($group, $religion) => (object)[
                    'label'   => $religion,
                    'count'   => $group->count(),
                    'members' => $group->map(fn($a) => $a->member->user->name)->join(', '),
                ])
                ->sortByDesc('count')
                ->values();
        }

        return view('admin.reports.occasions', compact('results', 'filter', 'days'));
    }

    public function annual(Request $request)
    {
        $year = $request->year ?? now()->year;

        $totalCollections = MonthlyFeeSubmission::where('status', 'approved')->where('year', $year)->sum('amount');
        $totalExpenses    = Expense::where('status', 'active')->whereYear('date', $year)->sum('amount');
        $totalIncome      = Income::where('status', 'active')->whereYear('date', $year)->sum('amount');
        $netBalance       = $totalCollections + $totalIncome - $totalExpenses;

        $monthlyBreakdown = collect(range(1, 12))->map(function ($month) use ($year) {
            return [
                'month'       => $month,
                'month_name'  => date('F', mktime(0, 0, 0, $month, 1)),
                'collected'   => MonthlyFeeSubmission::where('status', 'approved')->where('year', $year)->where('month', $month)->sum('amount'),
                'expenses'    => Expense::where('status', 'active')->whereYear('date', $year)->whereMonth('date', $month)->sum('amount'),
                'income'      => Income::where('status', 'active')->whereYear('date', $year)->whereMonth('date', $month)->sum('amount'),
            ];
        });

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.annual-pdf', compact('year', 'totalCollections', 'totalExpenses', 'totalIncome', 'netBalance', 'monthlyBreakdown'));
            return $pdf->download("annual-summary-{$year}.pdf");
        }

        return view('admin.reports.annual', compact('year', 'totalCollections', 'totalExpenses', 'totalIncome', 'netBalance', 'monthlyBreakdown'));
    }
}
