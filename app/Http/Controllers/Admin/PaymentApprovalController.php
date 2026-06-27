<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PaymentApprovedAdmin;
use App\Mail\PaymentReceipt;
use App\Mail\PaymentRejected;
use App\Models\AuditLog;
use App\Models\EmailLog;
use App\Models\MonthlyFeeSubmission;
use App\Models\Receipt;
use App\Support\MailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = MonthlyFeeSubmission::with('member.user');

        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        if ($request->search) {
            $query->whereHas('member.user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $submissions = $query->latest()->paginate(20);

        return view('admin.payments.index', compact('submissions'));
    }

    public function show(MonthlyFeeSubmission $submission)
    {
        $submission->load('member.user', 'receipt');
        $emailLogs = EmailLog::where('loggable_type', MonthlyFeeSubmission::class)
            ->where('loggable_id', $submission->id)
            ->latest()
            ->get();

        $receiptEmailSent = $emailLogs
            ->where('mailable_class', PaymentReceipt::class)
            ->where('status', 'sent')
            ->isNotEmpty();

        return view('admin.payments.show', compact('submission', 'emailLogs', 'receiptEmailSent'));
    }

    public function approve(Request $request, MonthlyFeeSubmission $submission)
    {
        if (!$submission->isPending()) {
            return back()->with('error', 'Payment is not pending.');
        }

        $request->validate(['approval_remarks' => 'nullable|string|max:500']);

        DB::transaction(function () use ($submission, $request) {
            $submission->update([
                'status'           => 'approved',
                'approved_by'      => auth()->id(),
                'approved_at'      => now(),
                'approval_remarks' => $request->approval_remarks,
                'updated_by'       => auth()->id(),
            ]);

            $member = $submission->member;

            $receipt = Receipt::create([
                'receipt_number'            => Receipt::generateReceiptNumber(),
                'monthly_fee_submission_id' => $submission->id,
                'member_id'                 => $member->id,
                'member_name'               => $member->user->name,
                'month'                     => $submission->month,
                'year'                      => $submission->year,
                'amount'                    => $submission->amount,
                'payment_method'            => $submission->payment_method,
                'payment_date'              => $submission->payment_date,
                'approved_date'             => now()->toDateString(),
                'authorized_by'             => auth()->user()->name,
            ]);

            $submission->update(['receipt_id' => $receipt->id]);

            AuditLog::record('payment_approved', $submission, [], [], "Approved payment for {$member->user->name}");

            if (MailHelper::validEmail($member->user->email)) {
                MailHelper::send(
                    $member->user->email, $member->user->name,
                    new PaymentReceipt($receipt),
                    $submission, auth()->id()
                );
            }

            MailHelper::sendToAdmins(
                fn() => new PaymentApprovedAdmin($receipt),
                $submission, auth()->id()
            );
        });

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment approved and receipt generated.');
    }

    public function reject(Request $request, MonthlyFeeSubmission $submission)
    {
        if (!$submission->isPending()) {
            return back()->with('error', 'Payment is not pending.');
        }

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $submission->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'updated_by'       => auth()->id(),
        ]);

        AuditLog::record('payment_rejected', $submission, [], [], "Rejected payment for " . $submission->member->user->name);

        $memberUser = $submission->member->user;
        if (MailHelper::validEmail($memberUser->email)) {
            MailHelper::send(
                $memberUser->email, $memberUser->name,
                new PaymentRejected($submission),
                $submission, auth()->id()
            );
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment rejected.');
    }
}
