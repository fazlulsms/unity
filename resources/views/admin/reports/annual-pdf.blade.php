<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Annual Summary {{ $year }}</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 20px; }
    h1 { font-size: 16px; margin-bottom: 4px; }
    .subtitle { color: #555; font-size: 10px; margin-bottom: 16px; }
    .summary-grid { display: table; width: 100%; margin-bottom: 20px; }
    .summary-box { display: table-cell; width: 25%; padding: 10px; border: 1px solid #ddd; background: #f7f9fc; text-align: center; }
    .summary-box .label { font-size: 9px; color: #555; }
    .summary-box .value { font-size: 14px; font-weight: bold; margin-top: 4px; }
    .blue { color: #1e3a5f; }
    .red { color: #c0392b; }
    .green { color: #1a7a4a; }
    .teal { color: #0e7490; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #1e3a5f; color: #fff; text-align: left; padding: 6px 8px; font-size: 10px; }
    td { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; }
    tr:nth-child(even) td { background: #f7f9fc; }
    tfoot td { font-weight: bold; background: #eef2f7; border-top: 2px solid #c0cfe0; }
    .footer { margin-top: 20px; font-size: 9px; color: #888; border-top: 1px solid #ddd; padding-top: 8px; }
</style>
</head>
<body>
    <h1>Unity Club — Annual Fund Summary {{ $year }}</h1>
    <p class="subtitle">Generated: {{ now()->format('d M Y, g:i A') }}</p>

    <div class="summary-grid">
        <div class="summary-box">
            <div class="label">Total Collections</div>
            <div class="value blue">৳{{ number_format($totalCollections, 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total Expenses</div>
            <div class="value red">৳{{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Other Income</div>
            <div class="value teal">৳{{ number_format($totalIncome, 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Net Balance</div>
            <div class="value {{ $netBalance >= 0 ? 'green' : 'red' }}">৳{{ number_format($netBalance, 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Collections</th>
                <th>Expenses</th>
                <th>Income</th>
                <th>Net</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyBreakdown as $row)
            @php $net = $row['collected'] + $row['income'] - $row['expenses']; @endphp
            <tr>
                <td>{{ $row['month_name'] }}</td>
                <td>৳{{ number_format($row['collected'], 2) }}</td>
                <td>৳{{ number_format($row['expenses'], 2) }}</td>
                <td>৳{{ number_format($row['income'], 2) }}</td>
                <td class="{{ $net >= 0 ? 'green' : 'red' }}">৳{{ number_format($net, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td>৳{{ number_format($totalCollections, 2) }}</td>
                <td>৳{{ number_format($totalExpenses, 2) }}</td>
                <td>৳{{ number_format($totalIncome, 2) }}</td>
                <td>৳{{ number_format($netBalance, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    <div class="footer">Unity Club &nbsp;|&nbsp; Annual Financial Summary &nbsp;|&nbsp; Confidential</div>
</body>
</html>
