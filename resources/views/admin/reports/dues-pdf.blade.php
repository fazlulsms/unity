<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Due Report</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 20px; }
    h1 { font-size: 16px; margin-bottom: 4px; }
    .subtitle { color: #555; font-size: 10px; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #1e3a5f; color: #fff; text-align: left; padding: 6px 8px; font-size: 10px; }
    td { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; }
    tr:nth-child(even) td { background: #f7f9fc; }
    .due { color: #c0392b; font-weight: bold; }
    .footer { margin-top: 20px; font-size: 9px; color: #888; border-top: 1px solid #ddd; padding-top: 8px; }
</style>
</head>
<body>
    <h1>Unity Club — Due Report</h1>
    <p class="subtitle">Generated: {{ now()->format('d M Y, g:i A') }} &nbsp;|&nbsp; Members with dues: {{ $members->count() }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Member No.</th>
                <th>Monthly Fee</th>
                <th>Expected Total</th>
                <th>Paid</th>
                <th>Due</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $i => $m)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $m['user_name'] }}</td>
                <td>{{ $m['member_number'] }}</td>
                <td>৳{{ number_format($m['monthly_fee_amount'], 2) }}</td>
                <td>৳{{ number_format($m['expected_total'], 2) }}</td>
                <td>৳{{ number_format($m['paid_total'], 2) }}</td>
                <td class="due">৳{{ number_format($m['due_amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">Unity Club &nbsp;|&nbsp; Confidential financial information</div>
</body>
</html>
