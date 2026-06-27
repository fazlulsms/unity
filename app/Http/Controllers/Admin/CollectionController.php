<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PaymentReceipt;
use App\Models\AuditLog;
use App\Models\EmailLog;
use App\Models\Member;
use App\Models\MonthlyFeeSubmission;
use App\Models\Receipt;
use App\Support\MailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    // ── Collection history + summary ─────────────────────────────────────────

    public function index(Request $request)
    {
        $query = MonthlyFeeSubmission::with('member.user', 'receipt')
            ->where('status', 'approved');

        if ($request->search) {
            $query->whereHas('member.user', fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
            );
        }
        if ($request->month) $query->where('month', $request->month);
        if ($request->year)  $query->where('year',  $request->year);

        $collections = $query->latest()->paginate(25)->withQueryString();

        $summary = [
            'this_month' => MonthlyFeeSubmission::where('status', 'approved')
                ->where('month', now()->month)->where('year', now()->year)->sum('amount'),
            'this_year'  => MonthlyFeeSubmission::where('status', 'approved')
                ->where('year', now()->year)->sum('amount'),
            'total'      => MonthlyFeeSubmission::where('status', 'approved')->sum('amount'),
            'count_this_month' => MonthlyFeeSubmission::where('status', 'approved')
                ->where('month', now()->month)->where('year', now()->year)->count(),
        ];

        return view('admin.collections.index', compact('collections', 'summary'));
    }

    // ── View single payment detail ───────────────────────────────────────────

    public function show(MonthlyFeeSubmission $collection)
    {
        $collection->load('member.user', 'receipt', 'approver', 'creator');

        $emailLogs = EmailLog::where('loggable_type', MonthlyFeeSubmission::class)
            ->where('loggable_id', $collection->id)
            ->latest()->get();

        $receiptEmailSent = $emailLogs
            ->where('mailable_class', \App\Mail\PaymentReceipt::class)
            ->where('status', 'sent')
            ->isNotEmpty();

        return view('admin.collections.show', compact('collection', 'emailLogs', 'receiptEmailSent'));
    }

    // ── Add single manual payment ────────────────────────────────────────────

    public function create(Request $request)
    {
        $members  = Member::with('user')->where('status', 'active')
            ->orderBy('id')->get();
        $selected = $request->member ? Member::with('user')->find($request->member) : null;

        // Load all approved payments for active members in one query for the rich right panel
        $memberIds   = $members->pluck('id');
        $allPayments = MonthlyFeeSubmission::where('status', 'approved')
            ->whereIn('member_id', $memberIds)
            ->orderByDesc('payment_date')
            ->get(['member_id', 'month', 'year', 'amount', 'payment_date', 'payment_method']);

        $paymentsByMember = $allPayments->groupBy('member_id');

        $memberData = $members->mapWithKeys(function ($m) use ($paymentsByMember) {
            $payments = $paymentsByMember->get($m->id) ?? collect();

            $recent = $payments->take(5)->map(fn($s) => [
                'month_name' => date('F', mktime(0, 0, 0, $s->month, 1)),
                'month'      => (int) $s->month,
                'year'       => (int) $s->year,
                'amount'     => (float) $s->amount,
                'date'       => \Carbon\Carbon::parse($s->payment_date)->format('d M Y'),
                'method'     => ucfirst($s->payment_method),
            ])->values()->all();

            $paidMonths = $payments->map(fn($s) =>
                $s->year . '-' . str_pad($s->month, 2, '0', STR_PAD_LEFT)
            )->unique()->values()->all();

            return [$m->id => [
                'name'          => $m->user->name,
                'number'        => $m->member_number,
                'phone'         => $m->user->phone ?? '',
                'email'         => $m->user->email ?? '',
                'photo'         => $m->user->photo_url,
                'status'        => $m->status,
                'join_date'     => $m->join_date->format('d M Y'),
                'monthly_fee'   => (float) $m->monthly_fee_amount,
                'total_payable' => (float) $m->total_payable,
                'total_paid'    => (float) $m->total_paid,
                'total_due'     => (float) $m->total_due,
                'payments'      => $recent,
                'paid_months'   => $paidMonths,
                'profile_url'   => route('admin.members.show', $m->id),
                'statement_url' => route('admin.members.statement', $m->id),
            ]];
        });

        return view('admin.collections.create', compact('members', 'selected', 'memberData'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'             => 'required|exists:members,id',
            'month'                 => 'required|integer|between:1,12',
            'year'                  => 'required|integer|min:2020|max:' . (now()->year + 1),
            'amount'                => 'required|numeric|min:0.01',
            'payment_date'          => 'required|date',
            'payment_method'        => 'required|in:cash,bank,bkash,nagad,rocket,other',
            'transaction_reference' => 'nullable|string|max:100',
            'proof_attachment'      => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'                 => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('proof_attachment')) {
            $file = $request->file('proof_attachment');
            $filename = $file->hashName();
            $base = rtrim($_SERVER['DOCUMENT_ROOT'] ?? public_path(), '/');
            $dir = $base . '/uploads/collections';
            @mkdir($dir, 0755, true);
            $file->move($dir, $filename);
            $data['proof_attachment'] = 'collections/' . $filename;
        }

        $member = Member::findOrFail($data['member_id']);

        $receipt    = null;
        $submission = null;

        DB::transaction(function () use ($data, $member, &$submission, &$receipt) {
            $submission = MonthlyFeeSubmission::create(array_merge($data, [
                'user_id'          => $member->user_id,
                'status'           => 'approved',
                'approved_by'      => auth()->id(),
                'approved_at'      => now(),
                'approval_remarks' => 'Manual entry by ' . auth()->user()->name,
                'created_by'       => auth()->id(),
            ]));

            $receipt = $this->issueReceipt($submission, $member);

            AuditLog::record('manual_payment_added', $submission, [], [],
                "Manual payment added for {$member->member_number} — "
                . date('F', mktime(0, 0, 0, $data['month'], 1)) . " {$data['year']}");
        });

        if ($receipt && $submission && MailHelper::validEmail($member->user->email ?? null)) {
            MailHelper::send(
                $member->user->email, $member->user->name,
                new PaymentReceipt($receipt),
                $submission, auth()->id()
            );
        }

        return redirect()->route('admin.collections.index')
            ->with('success', 'Payment recorded and receipt generated for ' . $member->user->name . '.');
    }

    // ── Bulk monthly collection entry ────────────────────────────────────────

    public function bulk(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year',  now()->year);

        $members = Member::with('user')->where('status', 'active')->orderBy('id')->get();

        $existing = MonthlyFeeSubmission::where('status', 'approved')
            ->where('month', $month)->where('year', $year)
            ->whereIn('member_id', $members->pluck('id'))
            ->get()->keyBy('member_id');

        return view('admin.collections.bulk', compact('members', 'existing', 'month', 'year'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'month'   => 'required|integer|between:1,12',
            'year'    => 'required|integer|min:2020',
            'entries' => 'nullable|array',
        ]);

        $month   = (int) $request->month;
        $year    = (int) $request->year;
        $entries = $request->input('entries', []);
        $saved   = 0;
        $toEmail = [];

        DB::transaction(function () use ($entries, $month, $year, &$saved, &$toEmail) {
            foreach ($entries as $memberId => $row) {
                if (empty($row['amount']) || (float) $row['amount'] <= 0) continue;

                $member = Member::with('user')->find($memberId);
                if (!$member) continue;

                $alreadyPaid = MonthlyFeeSubmission::where('member_id', $memberId)
                    ->where('month', $month)->where('year', $year)
                    ->where('status', 'approved')->exists();
                if ($alreadyPaid) continue;

                $submission = MonthlyFeeSubmission::create([
                    'member_id'             => $memberId,
                    'user_id'               => $member->user_id,
                    'month'                 => $month,
                    'year'                  => $year,
                    'amount'                => $row['amount'],
                    'payment_date'          => $row['payment_date'] ?? now()->toDateString(),
                    'payment_method'        => $row['payment_method'] ?? 'cash',
                    'transaction_reference' => $row['reference'] ?? null,
                    'notes'                 => $row['notes'] ?? null,
                    'status'                => 'approved',
                    'approved_by'           => auth()->id(),
                    'approved_at'           => now(),
                    'approval_remarks'      => 'Bulk collection entry',
                    'created_by'            => auth()->id(),
                ]);

                $receipt = $this->issueReceipt($submission, $member);
                $toEmail[] = ['receipt' => $receipt, 'submission' => $submission, 'member' => $member];
                $saved++;
            }
        });

        // Send receipt emails after transaction completes
        foreach ($toEmail as $item) {
            if (MailHelper::validEmail($item['member']->user->email ?? null)) {
                MailHelper::send(
                    $item['member']->user->email, $item['member']->user->name,
                    new PaymentReceipt($item['receipt']),
                    $item['submission'], auth()->id()
                );
            }
        }

        return redirect()->route('admin.collections.bulk', ['month' => $month, 'year' => $year])
            ->with('success', "{$saved} payment(s) recorded for " . date('F', mktime(0, 0, 0, $month, 1)) . " {$year}.");
    }

    // ── Due list ─────────────────────────────────────────────────────────────

    public function due()
    {
        $members = Member::with(['user',
                'feeSubmissions' => fn($q) => $q->where('status', 'approved')
            ])
            ->where('status', 'active')
            ->orderBy('id')
            ->get()
            ->map(function ($member) {
                $paid     = (float) $member->feeSubmissions->sum('amount');
                $expected = $member->total_payable;
                $due      = max(0.0, $expected - $paid);

                $lastPayment = $member->feeSubmissions->sortByDesc('payment_date')->first();

                return [
                    'member'       => $member,
                    'expected'     => $expected,
                    'paid'         => $paid,
                    'due'          => $due,
                    'last_payment' => $lastPayment,
                ];
            })
            ->filter(fn($r) => $r['due'] > 0)
            ->sortByDesc('due')
            ->values();

        $totalDue = $members->sum('due');

        return view('admin.collections.due', compact('members', 'totalDue'));
    }

    // ── Private: issue receipt for a submission ───────────────────────────────

    private function issueReceipt(MonthlyFeeSubmission $submission, Member $member): Receipt
    {
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

        return $receipt;
    }
}
