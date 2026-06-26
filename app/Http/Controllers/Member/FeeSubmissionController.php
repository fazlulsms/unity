<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Mail\PaymentSubmitted;
use App\Mail\PaymentSubmittedAdmin;
use App\Models\MonthlyFeeSubmission;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
            $file = $request->file('proof_attachment');
            $filename = $file->hashName();
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir = $base . '/uploads/fee-proofs';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['proof_attachment'] = 'fee-proofs/' . $filename;
        }

        $data['member_id']  = $member->id;
        $data['user_id']    = Auth::id();
        $data['created_by'] = Auth::id();
        $data['status']     = 'pending';

        $submission = MonthlyFeeSubmission::create($data);

        // Email confirmation to member
        $memberEmail = Auth::user()->email;
        if ($memberEmail && !str_ends_with($memberEmail, '@unity.local')) {
            try {
                Mail::to($memberEmail)->send(new PaymentSubmitted($submission));
            } catch (\Exception $e) {
                logger()->error('Payment submitted email (member) failed: ' . $e->getMessage());
            }
        }

        // Email notification to admins and treasurers
        $admins = User::role(['admin', 'treasurer'])
            ->whereNotNull('email')
            ->where('email', 'not like', '%@unity.local')
            ->get();
        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new PaymentSubmittedAdmin($submission));
            } catch (\Exception $e) {
                logger()->error("Payment submitted admin email failed for {$admin->email}: " . $e->getMessage());
            }
        }

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
