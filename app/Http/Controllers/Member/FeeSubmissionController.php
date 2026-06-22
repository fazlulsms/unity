<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\MonthlyFeeSubmission;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeSubmissionController extends Controller
{
    public function index()
    {
        $member = Auth::user()->member;

        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }

        $submissions = $member->feeSubmissions()->with('receipt')->latest()->paginate(15);
        $totalPaid   = $member->feeSubmissions()->where('status', 'approved')->sum('amount');
        $totalDue    = $member->total_due;

        return view('member.fees.index', compact('submissions', 'totalPaid', 'totalDue', 'member'));
    }

    public function create()
    {
        $member = Auth::user()->member;

        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }

        return view('member.fees.create', compact('member'));
    }

    public function store(Request $request)
    {
        $member = Auth::user()->member;

        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }

        $data = $request->validate([
            'month'                 => 'required|integer|between:1,12',
            'year'                  => 'required|integer|min:2020|max:' . (now()->year + 1),
            'amount'                => 'required|numeric|min:1',
            'payment_date'          => 'required|date|before_or_equal:today',
            'payment_method'        => 'required|in:cash,bank,bkash,nagad,rocket,other',
            'transaction_reference' => 'nullable|string|max:100',
            'proof_attachment'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'                 => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('proof_attachment')) {
            $data['proof_attachment'] = $request->file('proof_attachment')->store('fee-proofs', 'public');
        }

        $data['member_id']  = $member->id;
        $data['user_id']    = Auth::id();
        $data['created_by'] = Auth::id();
        $data['status']     = 'pending';

        MonthlyFeeSubmission::create($data);

        return redirect()->route('member.fees.index')
            ->with('success', 'Payment submitted successfully. It is pending admin approval.');
    }

    public function show(MonthlyFeeSubmission $submission)
    {
        $this->authorizeSubmission($submission);
        return view('member.fees.show', compact('submission'));
    }

    public function downloadReceipt(Receipt $receipt)
    {
        $member = Auth::user()->member;

        if (!$member || $receipt->member_id !== $member->id) {
            abort(403);
        }

        return view('member.fees.receipt', compact('receipt'));
    }

    public function statement(Request $request)
    {
        $member = Auth::user()->member;

        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }

        $year     = (int) $request->get('year', now()->year);
        $joinDate = $member->join_date;

        $startMonth = ($joinDate->year === $year) ? $joinDate->month : 1;
        $endMonth   = ($year >= now()->year) ? now()->month : 12;

        $rows = [];

        if ($joinDate->year <= $year) {
            $submissions = $member->approvedFeeSubmissions()
                ->where('year', $year)
                ->with('receipt')
                ->get()
                ->keyBy(fn($s) => str_pad($s->month, 2, '0', STR_PAD_LEFT));

            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $key = str_pad($m, 2, '0', STR_PAD_LEFT);
                $sub = $submissions->get($key);

                $expected = (float) $member->monthly_fee_amount;
                $paid     = $sub ? (float) $sub->amount : 0.0;
                $due      = max(0.0, $expected - $paid);

                $rows[] = [
                    'month'          => $m,
                    'month_name'     => date('F', mktime(0, 0, 0, $m, 1)),
                    'expected'       => $expected,
                    'paid'           => $paid,
                    'due'            => $due,
                    'method'         => $sub ? ucfirst($sub->payment_method) : '—',
                    'payment_date'   => $sub?->payment_date?->format('d M Y') ?? '—',
                    'receipt_number' => $sub?->receipt?->receipt_number ?? '—',
                    'status'         => $paid >= $expected ? 'paid' : ($paid > 0 ? 'partial' : 'due'),
                ];
            }
        }

        $totals = [
            'expected' => collect($rows)->sum('expected'),
            'paid'     => collect($rows)->sum('paid'),
            'due'      => collect($rows)->sum('due'),
        ];

        $availableYears = range(max($joinDate->year, 2020), now()->year);

        return view('member.statement', compact('member', 'rows', 'totals', 'year', 'availableYears'));
    }

    private function authorizeSubmission(MonthlyFeeSubmission $submission): void
    {
        $member = Auth::user()->member;
        if (!$member || $submission->member_id !== $member->id) {
            abort(403);
        }
    }
}
