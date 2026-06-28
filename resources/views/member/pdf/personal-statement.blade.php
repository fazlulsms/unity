<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Personal Statement – {{ $member->member_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
    .page { padding: 30px 34px; }
    .org-header { text-align: center; border-bottom: 2px solid #1d4ed8; padding-bottom: 12px; margin-bottom: 18px; }
    .org-name { font-size: 20px; font-weight: bold; color: #1d4ed8; letter-spacing: 1px; }
    .doc-title { font-size: 13px; color: #374151; margin-top: 2px; }
    .member-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 12px 16px; margin-bottom: 16px; }
    .grid { display: table; width: 100%; }
    .col { display: table-cell; width: 50%; vertical-align: top; padding: 3px 0; }
    .label { color: #6b7280; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
    .value { color: #111827; font-weight: bold; font-size: 11px; margin-top: 2px; }
    .summary { display: table; width: 100%; margin: 14px 0; border-spacing: 8px; }
    .sum-cell { display: table-cell; text-align: center; border: 1px solid #e5e7eb; border-radius: 4px; padding: 9px 6px; }
    .sum-label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
    .sum-value { font-size: 15px; font-weight: bold; margin-top: 3px; }
    .green { color: #059669; } .red { color: #dc2626; } .gray { color: #374151; }
    h3.section { font-size: 11px; color: #1d4ed8; margin: 16px 0 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    thead th { background: #1d4ed8; color: #fff; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
    thead th.right { text-align: right; }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; }
    tbody td.right { text-align: right; }
    tbody tr:nth-child(even) { background: #f9fafb; }
    tfoot td { padding: 6px 8px; font-weight: bold; background: #f1f5f9; border-top: 2px solid #1d4ed8; }
    tfoot td.right { text-align: right; }
    .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 8px; font-weight: bold; }
    .badge-paid { background: #d1fae5; color: #065f46; }
    .badge-due { background: #fee2e2; color: #991b1b; }
    .badge-partial { background: #fef3c7; color: #92400e; }
    .footer { margin-top: 22px; border-top: 1px solid #e5e7eb; padding-top: 10px; text-align: center; color: #9ca3af; font-size: 9px; }
</style>
</head>
<body>
<div class="page">
    <div class="org-header">
        <div class="org-name">Unity Circle</div>
        <div class="doc-title">Personal Member Statement — {{ $range->label }}</div>
    </div>

    <div class="member-box">
        <div class="grid">
            <div class="col"><div class="label">Member Name</div><div class="value">{{ $member->user->name }}</div></div>
            <div class="col"><div class="label">Member ID</div><div class="value">{{ $member->member_number }}</div></div>
        </div>
        <div class="grid" style="margin-top:6px;">
            <div class="col"><div class="label">Monthly Fee</div><div class="value">Tk. {{ number_format($member->monthly_fee_amount, 2) }}</div></div>
            <div class="col"><div class="label">Generated</div><div class="value">{{ now()->format('d M Y') }}</div></div>
        </div>
    </div>

    <div class="summary">
        <div class="sum-cell"><div class="sum-label">Total Expected</div><div class="sum-value gray">Tk. {{ number_format($totals['expected'], 2) }}</div></div>
        <div class="sum-cell"><div class="sum-label">Total Paid</div><div class="sum-value green">Tk. {{ number_format($totals['paid'], 2) }}</div></div>
        <div class="sum-cell"><div class="sum-label">Outstanding</div><div class="sum-value {{ $totals['due'] > 0 ? 'red' : 'gray' }}">Tk. {{ number_format($totals['due'], 2) }}</div></div>
    </div>

    <h3 class="section">Monthly Fees — {{ $range->label }}</h3>
    <table>
        <thead>
            <tr>
                <th>Month</th><th class="right">Expected</th><th class="right">Paid</th><th class="right">Due</th>
                <th>Method</th><th>Date</th><th>Receipt</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row['month_name'] }}</td>
                <td class="right">Tk. {{ number_format($row['expected'], 2) }}</td>
                <td class="right">{{ $row['paid'] > 0 ? 'Tk. ' . number_format($row['paid'], 2) : '—' }}</td>
                <td class="right">{{ $row['due'] > 0 ? 'Tk. ' . number_format($row['due'], 2) : '—' }}</td>
                <td>{{ $row['method'] }}</td>
                <td>{{ $row['payment_date'] }}</td>
                <td style="font-family: monospace; font-size: 9px;">{{ $row['receipt_number'] }}</td>
                <td>
                    @if($row['status'] === 'paid')<span class="badge badge-paid">Paid</span>
                    @elseif($row['status'] === 'partial')<span class="badge badge-partial">Partial</span>
                    @else<span class="badge badge-due">Due</span>@endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center; color:#9ca3af; padding:16px;">No monthly records for {{ $range->label }}.</td></tr>
            @endforelse
            @if($totals['joining_contribution'] > 0)
            <tr style="background:#fffbeb;">
                <td><strong>Joining Contribution</strong></td>
                <td class="right">Tk. {{ number_format($totals['joining_contribution'], 2) }}</td>
                <td class="right">—</td><td class="right">Tk. {{ number_format($totals['joining_contribution'], 2) }}</td>
                <td colspan="4"></td>
            </tr>
            @endif
        </tbody>
        @if(count($rows) > 0)
        <tfoot>
            <tr>
                <td>Monthly Total</td>
                <td class="right">Tk. {{ number_format($totals['monthly_expected'], 2) }}</td>
                <td class="right" style="color:#059669;">Tk. {{ number_format($totals['monthly_paid'], 2) }}</td>
                <td class="right" style="{{ $totals['monthly_due'] > 0 ? 'color:#dc2626;' : 'color:#9ca3af;' }}">Tk. {{ number_format($totals['monthly_due'], 2) }}</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <h3 class="section">Booster Contribution</h3>
    <div class="summary">
        <div class="sum-cell"><div class="sum-label">Expected</div><div class="sum-value gray">Tk. {{ number_format($totals['booster_expected'], 2) }}</div></div>
        <div class="sum-cell"><div class="sum-label">Paid</div><div class="sum-value green">Tk. {{ number_format($totals['booster_paid'], 2) }}</div></div>
        <div class="sum-cell"><div class="sum-label">Due</div><div class="sum-value {{ $totals['booster_due'] > 0 ? 'red' : 'gray' }}">Tk. {{ number_format($totals['booster_due'], 2) }}</div></div>
    </div>
    <table>
        <thead>
            <tr><th>Drive</th><th>Date</th><th>Method</th><th>Reference</th><th class="right">Amount</th></tr>
        </thead>
        <tbody>
            @forelse($boosterRows as $b)
            <tr>
                <td>{{ $b['title'] }}</td>
                <td>{{ $b['date'] }}</td>
                <td>{{ $b['method'] }}</td>
                <td>{{ $b['reference'] ?: '—' }}</td>
                <td class="right">Tk. {{ number_format($b['amount'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#9ca3af; padding:14px;">No booster payments recorded.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Confidential — Unity Circle · Computer-generated statement. No signature required.</div>
</div>
</body>
</html>
