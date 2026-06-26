<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PaymentApprovedAdmin;
use App\Mail\PaymentRejected;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\MonthlyFeeSubmission;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
        return view('admin.payments.show', compact('submission'));
    }

    public function approve(Request $request, MonthlyFeeSubmission $submission)
    {
        if (!$submission->isPending()) {
            return back()->with('error', 'Payment is not pending.');
        }

        $request->validate([
            'approval_remarks' => 'nullable|string|max:500',
        ]);

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
                'receipt_number'           => Receipt::generateReceiptNumber(),
                'monthly_fee_submission_id' => $submission->id,
                'member_id'                => $member->id,
                'member_name'              => $member->user->name,
                'month'                    => $submission->month,
                'year'                     => $submission->year,
                'amount'                   => $submission->amount,
                'payment_method'           => $submission->payment_method,
                'payment_date'             => $submission->payment_date,
                'approved_date'            => now()->toDateString(),
                'authorized_by'            => auth()->user()->name,
            ]);

            $submission->update(['receipt_id' => $receipt->id]);

            AuditLog::record('payment_approved', $submission, [], [], "Approved payment for {$member->user->name}");

            if ($member->user->email && !str_ends_with($member->user->email, '@unity.local')) {
                try {
                    Mail::to($member->user->email)->send(new \App\Mail\PaymentReceipt($receipt));
                } catch (\Exception $e) {
                    logger()->error('Receipt email failed: ' . $e->getMessage());
                }
            }

            // Notify admins and treasurers
            $admins = User::role(['admin', 'treasurer'])
                ->whereNotNull('email')
                ->where('email', 'not like', '%@unity.local')
                ->get();
            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new PaymentApprovedAdmin($receipt));
                } catch (\Exception $e) {
                    logger()->error("Payment approved admin email failed for {$admin->email}: " . $e->getMessage());
                }
            }
        });

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment approved and receipt generated.');
    }

    public function reject(Request $request, MonthlyFeeSubmission $submission)
    {
        if (!$submission->isPending()) {
            return back()->with('error', 'Payment is not pending.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $submission->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'updated_by'       => auth()->id(),
        ]);

        AuditLog::record('payment_rejected', $submission, [], [], "Rejected payment for " . $submission->member->user->name);

        $memberUser = $submission->member->user;
        if ($memberUser->email && !str_ends_with($memberUser->email, '@unity.local')) {
            try {
                Mail::to($memberUser->email)->send(new PaymentRejected($submission));
            } catch (\Exception $e) {
                logger()->error('Payment rejected email failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment rejected.');
    }
}
