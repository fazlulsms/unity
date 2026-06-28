<?php

namespace App\Support;

use App\Models\Member;
use Carbon\Carbon;

/**
 * Builds a member's personal contribution statement — monthly fees plus
 * Booster Contribution (both treated as direct member contribution) — scoped
 * to a selected reporting period.
 */
class MemberStatement
{
    public static function personal(Member $member, DateRange $range): array
    {
        $join = $member->join_date->copy()->startOfMonth();
        $nowM = Carbon::now()->startOfMonth();

        // Window of months to itemise, bounded by join date and today.
        $start = $range->from ? $range->from->copy()->startOfMonth() : $join->copy();
        if ($start->lt($join)) {
            $start = $join->copy();
        }
        $end = $range->to ? $range->to->copy()->startOfMonth() : $nowM->copy();
        if ($end->gt($nowM)) {
            $end = $nowM->copy();
        }

        $subs = $member->approvedFeeSubmissions()->with('receipt')->get()
            ->keyBy(fn($s) => $s->year . '-' . str_pad($s->month, 2, '0', STR_PAD_LEFT));

        $rows = [];
        if ($end->gte($start)) {
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $key = $cursor->year . '-' . str_pad($cursor->month, 2, '0', STR_PAD_LEFT);
                $sub = $subs->get($key);

                $expected = (float) $member->monthly_fee_amount;
                $paid     = $sub ? (float) $sub->amount : 0.0;

                $rows[] = [
                    'month_name'     => $cursor->format('M Y'),
                    'expected'       => $expected,
                    'paid'           => $paid,
                    'due'            => max(0.0, $expected - $paid),
                    'method'         => $sub ? ucfirst($sub->payment_method) : '—',
                    'payment_date'   => $sub?->payment_date?->format('d M Y') ?? '—',
                    'receipt_number' => $sub?->receipt?->receipt_number ?? '—',
                    'status'         => $paid >= $expected ? 'paid' : ($paid > 0 ? 'partial' : 'due'),
                ];
                $cursor->addMonthNoOverflow();
            }
        }

        // Joining contribution counts when the join date falls in the period.
        $joinIn = (!$range->from || $member->join_date->gte($range->from->copy()->startOfDay()))
            && (!$range->to || $member->join_date->lte($range->to->copy()->endOfDay()));
        $joinContribution = $joinIn ? (float) ($member->joining_contribution ?? 0) : 0.0;

        $monthlyExpected = (float) collect($rows)->sum('expected');
        $monthlyPaid     = (float) collect($rows)->sum('paid');

        // ── Booster contribution within the period ───────────────────────────
        $drives = $member->boosterContributions()
            ->when($range->from, fn($q) => $q->whereDate('period_date', '>=', $range->from))
            ->when($range->to, fn($q) => $q->whereDate('period_date', '<=', $range->to))
            ->get();
        $boosterExpected = (float) $drives->sum(fn($d) => (float) $d->pivot->expected_amount);

        $boosterPaymentsQuery = $member->boosterPayments()->with('boosterContribution')
            ->when($range->from, fn($q) => $q->whereDate('payment_date', '>=', $range->from))
            ->when($range->to, fn($q) => $q->whereDate('payment_date', '<=', $range->to))
            ->latest('payment_date')->latest('id');
        $boosterPaid = (float) (clone $boosterPaymentsQuery)->sum('amount');
        $boosterRows = $boosterPaymentsQuery->get()->map(fn($p) => [
            'title'     => $p->boosterContribution->title ?? 'Booster',
            'date'      => $p->payment_date->format('d M Y'),
            'method'    => ucfirst($p->payment_method),
            'amount'    => (float) $p->amount,
            'reference' => $p->reference,
        ])->all();

        $totals = [
            'joining_contribution' => $joinContribution,
            'monthly_expected'     => $monthlyExpected + $joinContribution,
            'monthly_paid'         => $monthlyPaid,
            'monthly_due'          => max(0.0, $monthlyExpected + $joinContribution - $monthlyPaid),
            'booster_expected'     => $boosterExpected,
            'booster_paid'         => $boosterPaid,
            'booster_due'          => max(0.0, $boosterExpected - $boosterPaid),
            'expected'             => $monthlyExpected + $joinContribution + $boosterExpected,
            'paid'                 => $monthlyPaid + $boosterPaid,
            'due'                  => max(0.0, $monthlyExpected + $joinContribution - $monthlyPaid)
                                      + max(0.0, $boosterExpected - $boosterPaid),
        ];

        return compact('rows', 'totals', 'boosterRows', 'range');
    }
}
