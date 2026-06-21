<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\MonthlyFeeSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%"));
        }

        $members = $query->latest()->paginate(20);

        return view('admin.members.index', compact('members'));
    }

    public function show(Member $member)
    {
        $member->load('user', 'feeSubmissions.receipt');
        $submissions = $member->feeSubmissions()->with('receipt')->latest()->paginate(12);

        return view('admin.members.show', compact('member', 'submissions'));
    }

    public function edit(Member $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'monthly_fee_amount' => 'required|numeric|min:0',
            'join_date'          => 'required|date',
            'status'             => 'required|in:active,inactive,suspended',
            'notes'              => 'nullable|string|max:1000',
        ]);

        $old = $member->toArray();
        $data['updated_by'] = auth()->id();

        $member->update($data);

        $userValidation = $request->validate([
            'name'    => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
        ]);

        $member->user->update(array_filter($userValidation));

        AuditLog::record('member_updated', $member, $old, $member->fresh()->toArray());

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Member updated successfully.');
    }

    public function addPayment(Request $request, Member $member)
    {
        $data = $request->validate([
            'month'                 => 'required|integer|between:1,12',
            'year'                  => 'required|integer|min:2020|max:' . (now()->year + 1),
            'amount'                => 'required|numeric|min:1',
            'payment_date'          => 'required|date',
            'payment_method'        => 'required|in:cash,bank,bkash,nagad,rocket,other',
            'transaction_reference' => 'nullable|string|max:100',
            'notes'                 => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($member, $data) {
            $submission = MonthlyFeeSubmission::create(array_merge($data, [
                'member_id'  => $member->id,
                'user_id'    => $member->user_id,
                'status'     => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_remarks' => 'Manual entry by admin',
                'created_by' => auth()->id(),
            ]));

            $this->generateReceipt($submission, $member);

            AuditLog::record('manual_payment_added', $submission, [], [], "Manual payment added for member {$member->member_number}");
        });

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Payment added and receipt generated.');
    }

    private function generateReceipt(MonthlyFeeSubmission $submission, Member $member): void
    {
        $receipt = \App\Models\Receipt::create([
            'receipt_number'           => \App\Models\Receipt::generateReceiptNumber(),
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
    }
}
