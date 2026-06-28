<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Support\DateRange;
use App\Support\FinanceSummary;
use App\Support\MemberStatement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatementController extends Controller
{
    /** Downloads hub: personal + club finance statements. */
    public function index(Request $request)
    {
        $member = Auth::user()->member;
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }

        $range = DateRange::fromRequest($request, 'this_year');

        return view('member.statements.index', compact('member', 'range'));
    }

    /** Personal member statement as PDF (respects the selected period). */
    public function personalPdf(Request $request)
    {
        $member = Auth::user()->member;
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }

        $range = DateRange::fromRequest($request, 'this_year');
        $data  = MemberStatement::personal($member, $range);

        $pdf = Pdf::loadView('member.pdf.personal-statement', array_merge($data, compact('member')));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("my-statement-{$member->member_number}.pdf");
    }

    /** Club finance statement — read-only view. */
    public function clubFinance(Request $request)
    {
        $member = Auth::user()->member;
        $range  = DateRange::fromRequest($request, 'all');
        $data   = $this->clubFinanceData($range);

        return view('member.statements.club-finance', array_merge($data, compact('member', 'range')));
    }

    /** Club finance statement as PDF (respects the selected period). */
    public function clubFinancePdf(Request $request)
    {
        $range = DateRange::fromRequest($request, 'all');
        $data  = $this->clubFinanceData($range);

        $pdf = Pdf::loadView('member.pdf.club-finance-statement', array_merge($data, compact('range')));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('club-finance-statement-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Club-wide finance figures + bank-wise + FDR breakdown for a period.
     * Flows are within the range; positions are as of the period end.
     */
    private function clubFinanceData(DateRange $range): array
    {
        $from = $range->from;
        $to   = $range->to;
        $asOf = $range->asOf();

        $summary  = FinanceSummary::all($from, $to);
        $accounts = BankAccount::orderBy('bank_name')->get();

        $bankRows = $accounts->map(fn($a) => [
            'account'   => $a,
            'deposited' => $a->depositsBetween($from, $to),
            'available' => $a->availableBalanceAsOf($asOf),
            'activeFdr' => $a->activeFdrAsOf($asOf),
            'interest'  => $a->fdrInterestBetween($from, $to),
            'withdrawn' => $a->withdrawalsBetween($from, $to),
        ]);

        $fdrSummary = [
            'active_count'    => $summary['fdr_created']['count'],
            'active_amount'   => $summary['fdr_created']['amount'],
            'closed_count'    => $summary['fdr_closed']['count'],
            'interest_earned' => $summary['total_fdr_interest'],
        ];

        return compact('summary', 'bankRows', 'fdrSummary');
    }
}
