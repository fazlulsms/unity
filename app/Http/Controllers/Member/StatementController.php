<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\FdrRecord;
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

        $year           = (int) $request->get('year', now()->year);
        $availableYears = range(max($member->join_date->year, 2020), now()->year);

        return view('member.statements.index', compact('member', 'year', 'availableYears'));
    }

    /** Personal member statement as PDF. */
    public function personalPdf(Request $request)
    {
        $member = Auth::user()->member;
        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }

        $year = (int) $request->get('year', now()->year);
        $data = MemberStatement::personal($member, $year);

        $pdf = Pdf::loadView('member.pdf.personal-statement', array_merge($data, compact('member')));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("my-statement-{$member->member_number}-{$year}.pdf");
    }

    /** Club finance statement — read-only view. */
    public function clubFinance()
    {
        $member = Auth::user()->member;
        $data   = $this->clubFinanceData();

        return view('member.statements.club-finance', array_merge($data, compact('member')));
    }

    /** Club finance statement as PDF. */
    public function clubFinancePdf()
    {
        $data = $this->clubFinanceData();

        $pdf = Pdf::loadView('member.pdf.club-finance-statement', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('club-finance-statement-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Assemble the club-wide finance figures + bank-wise + FDR breakdown
     * shared by the view and the PDF.
     */
    private function clubFinanceData(): array
    {
        $summary  = FinanceSummary::all();
        $accounts = BankAccount::orderBy('bank_name')->get();

        $fdrSummary = [
            'active_count'    => FdrRecord::where('status', 'active')->count(),
            'active_amount'   => (float) FdrRecord::where('status', 'active')->sum('principal_amount'),
            'closed_count'    => FdrRecord::whereIn('status', ['matured', 'closed', 'renewed'])->count(),
            'interest_earned' => $summary['total_fdr_interest'],
        ];

        return compact('summary', 'accounts', 'fdrSummary');
    }
}
