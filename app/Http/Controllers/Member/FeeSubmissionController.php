<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Mail\PaymentSubmitted;
use App\Mail\PaymentSubmittedAdmin;
use App\Models\MonthlyFeeSubmission;
use App\Models\Receipt;
use App\Support\MailHelper;
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

        $memberEmail = Auth::user()->email;
        if (MailHelper::validEmail($memberEmail)) {
            MailHelper::send(
                $memberEmail, Auth::user()->name,
                new PaymentSubmitted($submission),
                $submission, Auth::id()
            );
        }

        MailHelper::sendToAdmins(
            fn() => new PaymentSubmittedAdmin($submission),
            $submission, Auth::id()
        );

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
        $user = Auth::user();

        // Admins and treasurers can view any receipt
        if (!$user->isAdminOrTreasurer()) {
            $member = $user->member;
            if (!$member || $receipt->member_id !== $member->id) {
                abort(403);
            }
        }

        return view('member.fees.receipt', compact('receipt'));
    }

    public function statement(Request $request)
    {
        $member = Auth::user()->member;

        if (!$member) {
            return redirect()->route('member.dashboard')->with('error', 'Member profile not found.');
        }

        $year = (int) $request->get('year', now()->year);
        $data = \App\Support\MemberStatement::personal($member, $year);

        return view('member.statement', array_merge($data, compact('member')));
    }

    private function authorizeSubmission(MonthlyFeeSubmission $submission): void
    {
        $member = Auth::user()->member;
        if (!$member || $submission->member_id !== $member->id) {
            abort(403);
        }
    }
}
