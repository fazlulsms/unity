<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PaymentReceipt;
use App\Models\AuditLog;
use App\Models\EmailLog;
use App\Models\Member;
use App\Models\MemberProfileHistory;
use App\Models\MonthlyFeeSubmission;
use App\Models\User;
use App\Support\MailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->whereHas('user', fn($q) => $q
                ->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"));
        }

        $members = $query->latest()->paginate(20);

        return view('admin.members.index', compact('members'));
    }

    public function show(Member $member)
    {
        $member->load('user', 'application');
        $submissions  = $member->feeSubmissions()->with('receipt')->latest()->paginate(12);
        $lastPayment  = $member->approvedFeeSubmissions()->latest('payment_date')->first();

        $emailLogs = EmailLog::where(function ($q) use ($member) {
            $q->where('to_email', $member->user->email)
              ->orWhere(function ($q2) use ($member) {
                  $q2->where('loggable_type', Member::class)->where('loggable_id', $member->id);
              });
        })->latest()->get();

        // Submission IDs that already have a sent receipt email (for Send vs Resend button)
        $receiptEmailSentIds = EmailLog::where('loggable_type', MonthlyFeeSubmission::class)
            ->whereIn('loggable_id', $submissions->pluck('id'))
            ->where('mailable_class', PaymentReceipt::class)
            ->where('status', 'sent')
            ->pluck('loggable_id')
            ->flip()
            ->toArray();

        $profileHistories = MemberProfileHistory::with('updater')
            ->where('member_id', $member->id)
            ->latest()
            ->get();

        return view('admin.members.show', compact(
            'member', 'submissions', 'lastPayment', 'emailLogs', 'receiptEmailSentIds', 'profileHistories'
        ));
    }

    public function edit(Member $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $memberData = $request->validate([
            'monthly_fee_amount' => 'required|numeric|min:0',
            'join_date'          => 'required|date',
            'status'             => 'required|in:active,inactive,suspended',
            'notes'              => 'nullable|string|max:1000',
        ]);

        $userData = $request->validate([
            'name'              => 'required|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string|max:1000',
            'date_of_birth'     => 'nullable|date',
            'profession'        => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'nominee_name'      => 'nullable|string|max:255',
            'nominee_contact'   => 'nullable|string|max:255',
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file     = $request->file('photo');
            $filename = $file->hashName();
            $base     = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir      = $base . '/uploads/members';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $userData['photo'] = 'members/' . $filename;
        } else {
            unset($userData['photo']);
        }

        // Human-readable field labels for history display
        $fieldLabels = [
            'name'               => 'Name',
            'phone'              => 'Phone',
            'address'            => 'Address',
            'date_of_birth'      => 'Date of Birth',
            'profession'         => 'Profession',
            'emergency_contact'  => 'Emergency Contact',
            'nominee_name'       => 'Nominee Name',
            'nominee_contact'    => 'Nominee Contact',
            'photo'              => 'Photo',
            'monthly_fee_amount' => 'Monthly Fee',
            'join_date'          => 'Join Date',
            'status'             => 'Status',
            'notes'              => 'Notes',
        ];

        $user    = $member->user;
        $changes = [];

        // Detect user field changes
        foreach ($userData as $field => $newValue) {
            if ($field === 'photo') continue; // handled separately below
            $oldRaw = $user->$field;
            $oldStr = $oldRaw instanceof \Illuminate\Support\Carbon
                ? $oldRaw->format('Y-m-d')
                : ($oldRaw ?? '');
            $newStr = $newValue ?? '';
            if ($oldStr != $newStr) {
                $changes[$fieldLabels[$field] ?? $field] = ['old' => $oldStr, 'new' => $newStr];
            }
        }

        // Detect member field changes
        foreach ($memberData as $field => $newValue) {
            $oldRaw = $member->$field;
            $oldStr = $oldRaw instanceof \Illuminate\Support\Carbon
                ? $oldRaw->format('Y-m-d')
                : ((string) ($oldRaw ?? ''));
            $newStr = (string) ($newValue ?? '');
            if ($oldStr != $newStr) {
                $changes[$fieldLabels[$field] ?? $field] = ['old' => $oldStr, 'new' => $newStr];
            }
        }

        // Photo change
        if (isset($userData['photo'])) {
            $changes['Photo'] = ['old' => 'Previous photo', 'new' => 'New photo uploaded'];
        }

        $oldMember = $member->toArray();

        $memberData['updated_by'] = auth()->id();
        $member->update($memberData);
        $user->update($userData);

        // Record history if anything changed
        if (!empty($changes)) {
            MemberProfileHistory::create([
                'member_id'  => $member->id,
                'changes'    => $changes,
                'updated_by' => auth()->id(),
            ]);
        }

        AuditLog::record('member_updated', $member, $oldMember, $member->fresh()->toArray());

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Member profile updated.');
    }

    public function deactivate(Member $member)
    {
        if (!$member->isActive()) {
            return back()->with('error', 'Member is not currently active.');
        }
        $member->update(['status' => 'inactive', 'updated_by' => auth()->id()]);
        AuditLog::record('member_deactivated', $member, [], [], "Deactivated member {$member->member_number}");
        return back()->with('success', 'Member deactivated.');
    }

    public function reactivate(Member $member)
    {
        if ($member->isActive()) {
            return back()->with('error', 'Member is already active.');
        }
        $member->update(['status' => 'active', 'updated_by' => auth()->id()]);
        AuditLog::record('member_reactivated', $member, [], [], "Reactivated member {$member->member_number}");
        return back()->with('success', 'Member reactivated.');
    }

    public function statement(Request $request, Member $member)
    {
        $member->load('user');

        $year      = (int) $request->get('year', now()->year);
        $joinDate  = $member->join_date;

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

        if ($request->get('export') === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.member-statement-pdf',
                compact('member', 'rows', 'totals', 'year'));
            $pdf->setPaper('a4', 'portrait');
            return $pdf->download("statement-{$member->member_number}-{$year}.pdf");
        }

        return view('admin.members.statement', compact('member', 'rows', 'totals', 'year', 'availableYears'));
    }

    public function profilePdf(Member $member)
    {
        $member->load('user', 'application');

        $photoData = null;
        $photoPath = User::resolvedPhotoPath($member->user->photo);

        if ($photoPath) {
            $ext       = strtolower(pathinfo($photoPath, PATHINFO_EXTENSION));
            $mime      = $ext === 'png' ? 'image/png' : 'image/jpeg';
            $photoData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.member-profile-pdf',
            compact('member', 'photoData'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("member-profile-{$member->member_number}.pdf");
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

        $receipt    = null;
        $submission = null;

        DB::transaction(function () use ($member, $data, &$submission, &$receipt) {
            $submission = MonthlyFeeSubmission::create(array_merge($data, [
                'member_id'        => $member->id,
                'user_id'          => $member->user_id,
                'status'           => 'approved',
                'approved_by'      => auth()->id(),
                'approved_at'      => now(),
                'approval_remarks' => 'Manual entry by admin',
                'created_by'       => auth()->id(),
            ]));

            $receipt = $this->generateReceipt($submission, $member);

            AuditLog::record('manual_payment_added', $submission, [], [],
                "Manual payment added for {$member->member_number}");
        });

        if ($receipt && $submission && MailHelper::validEmail($member->user->email ?? null)) {
            MailHelper::send(
                $member->user->email, $member->user->name,
                new PaymentReceipt($receipt),
                $submission, auth()->id()
            );
        }

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Payment added and receipt generated.');
    }

    private function generateReceipt(MonthlyFeeSubmission $submission, Member $member): \App\Models\Receipt
    {
        $receipt = \App\Models\Receipt::create([
            'receipt_number'            => \App\Models\Receipt::generateReceiptNumber(),
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

        return $receipt;
    }
}
