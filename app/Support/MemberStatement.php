<?php

namespace App\Support;

use App\Models\Member;

/**
 * Builds a member's personal contribution statement — monthly fees plus
 * Booster Contribution (both treated as direct member contribution).
 */
class MemberStatement
{
    public static function personal(Member $member, int $year): array
    {
        $joinDate   = $member->join_date;
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

                $rows[] = [
                    'month'          => $m,
                    'month_name'     => date('F', mktime(0, 0, 0, $m, 1)),
                    'expected'       => $expected,
                    'paid'           => $paid,
                    'due'            => max(0.0, $expected - $paid),
                    'method'         => $sub ? ucfirst($sub->payment_method) : '—',
                    'payment_date'   => $sub?->payment_date?->format('d M Y') ?? '—',
                    'receipt_number' => $sub?->receipt?->receipt_number ?? '—',
                    'status'         => $paid >= $expected ? 'paid' : ($paid > 0 ? 'partial' : 'due'),
                ];
            }
        }

        $joiningContribution  = (float) ($member->joining_contribution ?? 0);
        $joinYearContribution = ($joinDate->year === $year) ? $joiningContribution : 0.0;
        $monthlyExpected      = (float) collect($rows)->sum('expected');
        $monthlyPaid          = (float) collect($rows)->sum('paid');

        // ── Booster contribution for this member (lifetime; drives span periods) ──
        $boosterExpected = (float) $member->booster_expected;
        $boosterPaid     = (float) $member->booster_paid;
        $boosterRows     = $member->boosterPayments()
            ->with('boosterContribution')
            ->latest('payment_date')->latest('id')->get()
            ->map(fn($p) => [
                'title'  => $p->boosterContribution->title ?? 'Booster',
                'date'   => $p->payment_date->format('d M Y'),
                'method' => ucfirst($p->payment_method),
                'amount' => (float) $p->amount,
                'reference' => $p->reference,
            ])->all();

        $totals = [
            'joining_contribution' => $joinYearContribution,
            'monthly_expected'     => $monthlyExpected + $joinYearContribution,
            'monthly_paid'         => $monthlyPaid,
            'monthly_due'          => max(0.0, $monthlyExpected + $joinYearContribution - $monthlyPaid),
            'booster_expected'     => $boosterExpected,
            'booster_paid'         => $boosterPaid,
            'booster_due'          => max(0.0, $boosterExpected - $boosterPaid),
            // Combined
            'expected'             => $monthlyExpected + $joinYearContribution + $boosterExpected,
            'paid'                 => $monthlyPaid + $boosterPaid,
            'due'                  => max(0.0, $monthlyExpected + $joinYearContribution - $monthlyPaid) + max(0.0, $boosterExpected - $boosterPaid),
        ];

        $availableYears = range(max($joinDate->year, 2020), now()->year);

        return compact('rows', 'totals', 'boosterRows', 'year', 'availableYears');
    }
}
