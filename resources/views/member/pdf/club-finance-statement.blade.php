<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Club Finance Statement</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
    .page { padding: 30px 34px; }
    .org-header { text-align: center; border-bottom: 2px solid #1d4ed8; padding-bottom: 12px; margin-bottom: 18px; }
    .org-name { font-size: 20px; font-weight: bold; color: #1d4ed8; letter-spacing: 1px; }
    .doc-title { font-size: 13px; color: #374151; margin-top: 2px; }
    h3.section { font-size: 11px; color: #1d4ed8; margin: 16px 0 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    .summary { display: table; width: 100%; border-spacing: 7px; }
    .srow { display: table-row; }
    .sum-cell { display: table-cell; width: 25%; text-align: center; border: 1px solid #e5e7eb; border-radius: 4px; padding: 9px 6px; }
    .sum-label { font-size: 8px; color: #6b7280; text-transform: uppercase; }
    .sum-value { font-size: 13px; font-weight: bold; margin-top: 3px; }
    .green { color: #059669; } .red { color: #dc2626; } .gray { color: #374151; } .violet { color: #7c3aed; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 4px; }
    thead th { background: #1d4ed8; color: #fff; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; }
    thead th.right { text-align: right; }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; }
    tbody td.right { text-align: right; }
    tbody tr:nth-child(even) { background: #f9fafb; }
    tfoot td { padding: 6px 8px; font-weight: bold; background: #f1f5f9; border-top: 2px solid #1d4ed8; }
    tfoot td.right { text-align: right; }
    .footer { margin-top: 22px; border-top: 1px solid #e5e7eb; padding-top: 10px; text-align: center; color: #9ca3af; font-size: 9px; }
</style>
</head>
<body>
<div class="page">
    <div class="org-header">
        <div class="org-name">Unity Circle</div>
        <div class="doc-title">Club Finance Statement — as of {{ now()->format('d M Y') }}</div>
    </div>

    <h3 class="section">Contributions &amp; Cash Flow</h3>
    <div class="summary">
        <div class="srow">
            <div class="sum-cell"><div class="sum-label">Member Collection</div><div class="sum-value gray">Tk. {{ number_format($summary['monthly_collection'], 0) }}</div></div>
            <div class="sum-cell"><div class="sum-label">Booster Collection</div><div class="sum-value gray">Tk. {{ number_format($summary['booster_collection'], 0) }}</div></div>
            <div class="sum-cell"><div class="sum-label">Total Member Contribution</div><div class="sum-value green">Tk. {{ number_format($summary['total_member_contribution'], 0) }}</div></div>
            <div class="sum-cell"><div class="sum-label">Bank Deposits</div><div class="sum-value gray">Tk. {{ number_format($summary['total_bank_deposits'], 0) }}</div></div>
        </div>
        <div class="srow">
            <div class="sum-cell"><div class="sum-label">Cash in Hand</div><div class="sum-value {{ $summary['cash_in_hand'] < 0 ? 'red' : 'gray' }}">Tk. {{ number_format($summary['cash_in_hand'], 0) }}</div></div>
            <div class="sum-cell"><div class="sum-label">Available Bank Balance</div><div class="sum-value green">Tk. {{ number_format($summary['total_available_balance'], 0) }}</div></div>
            <div class="sum-cell"><div class="sum-label">Active FDR</div><div class="sum-value violet">Tk. {{ number_format($summary['total_active_fdr'], 0) }}</div></div>
            <div class="sum-cell"><div class="sum-label">FDR Interest Earned</div><div class="sum-value green">Tk. {{ number_format($summary['total_fdr_interest'], 0) }}</div></div>
        </div>
        <div class="srow">
            <div class="sum-cell"><div class="sum-label">Other Income</div><div class="sum-value gray">Tk. {{ number_format($summary['total_other_income'], 0) }}</div></div>
            <div class="sum-cell"><div class="sum-label">Total Expenses</div><div class="sum-value red">Tk. {{ number_format($summary['total_expenses'], 0) }}</div></div>
            <div class="sum-cell"><div class="sum-label">Total Withdrawals</div><div class="sum-value red">Tk. {{ number_format($summary['total_withdrawals'], 0) }}</div></div>
            <div class="sum-cell"><div class="sum-label">Total Club Assets</div><div class="sum-value green">Tk. {{ number_format($summary['total_club_assets'], 0) }}</div></div>
        </div>
    </div>

    <h3 class="section">FDR Summary</h3>
    <table>
        <thead><tr><th>Active FDRs</th><th class="right">Active Principal</th><th>Closed/Matured FDRs</th><th class="right">Interest Earned</th></tr></thead>
        <tbody>
            <tr>
                <td>{{ $fdrSummary['active_count'] }}</td>
                <td class="right">Tk. {{ number_format($fdrSummary['active_amount'], 0) }}</td>
                <td>{{ $fdrSummary['closed_count'] }}</td>
                <td class="right">Tk. {{ number_format($fdrSummary['interest_earned'], 0) }}</td>
            </tr>
        </tbody>
    </table>

    <h3 class="section">Bank-wise Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Bank</th><th class="right">Deposited</th><th class="right">Available</th>
                <th class="right">Active FDR</th><th class="right">Interest</th><th class="right">Withdrawn</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $a)
            <tr>
                <td>{{ $a->bank_name }} ({{ $a->masked_account_number }})</td>
                <td class="right">Tk. {{ number_format($a->total_deposited, 0) }}</td>
                <td class="right">Tk. {{ number_format($a->available_balance, 0) }}</td>
                <td class="right">Tk. {{ number_format($a->active_fdr_amount, 0) }}</td>
                <td class="right">Tk. {{ number_format($a->fdr_interest_income, 0) }}</td>
                <td class="right">Tk. {{ number_format($a->total_withdrawn, 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center; color:#9ca3af; padding:14px;">No bank accounts.</td></tr>
            @endforelse
        </tbody>
        @if($accounts->isNotEmpty())
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="right">Tk. {{ number_format($accounts->sum('total_deposited'), 0) }}</td>
                <td class="right">Tk. {{ number_format($accounts->sum('available_balance'), 0) }}</td>
                <td class="right">Tk. {{ number_format($accounts->sum('active_fdr_amount'), 0) }}</td>
                <td class="right">Tk. {{ number_format($accounts->sum('fdr_interest_income'), 0) }}</td>
                <td class="right">Tk. {{ number_format($accounts->sum('total_withdrawn'), 0) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">Unity Circle · Read-only transparency statement · Computer-generated {{ now()->format('d M Y H:i') }}</div>
</div>
</body>
</html>
