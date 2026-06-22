<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\MonthlyFeeSubmission;
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

        return view('admin.members.show', compact('member', 'submissions', 'lastPayment'));
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

        $userFields = $request->validate([
            'name'    => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
        ]);
        $member->user->update(array_filter($userFields));

        AuditLog::record('member_updated', $member, $old, $member->fresh()->toArray());

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Member updated.');
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
        $photoPath = $member->user->photo
            ? storage_path('app/public/' . $member->user->photo)
            : null;

        if ($photoPath && file_exists($photoPath)) {
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

        DB::transaction(function () use ($member, $data) {
            $submission = MonthlyFeeSubmission::create(array_merge($data, [
                'member_id'        => $member->id,
                'user_id'          => $member->user_id,
                'status'           => 'approved',
                'approved_by'      => auth()->id(),
                'approved_at'      => now(),
                'approval_remarks' => 'Manual entry by admin',
                'created_by'       => auth()->id(),
            ]));

            $this->generateReceipt($submission, $member);

            AuditLog::record('manual_payment_added', $submission, [], [],
                "Manual payment added for {$member->member_number}");
        });

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'Payment added and receipt generated.');
    }

    private function generateReceipt(MonthlyFeeSubmission $submission, Member $member): void
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
    }
}
