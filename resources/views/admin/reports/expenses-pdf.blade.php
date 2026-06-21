<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Expense Report</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 20px; }
    h1 { font-size: 16px; margin-bottom: 4px; }
    .subtitle { color: #555; font-size: 10px; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #1e3a5f; color: #fff; text-align: left; padding: 6px 8px; font-size: 10px; }
    td { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; }
    tr:nth-child(even) td { background: #f7f9fc; }
    tfoot td { font-weight: bold; background: #eef2f7; border-top: 2px solid #c0cfe0; }
    .footer { margin-top: 20px; font-size: 9px; color: #888; border-top: 1px solid #ddd; padding-top: 8px; }
</style>
</head>
<body>
    <h1>Unity Club — Expense Report {{ $year }}</h1>
    <p class="subtitle">Generated: {{ now()->format('d M Y, g:i A') }} &nbsp;|&nbsp; Records: {{ $expenses->count() }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $i => $e)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $e->date->format('d M Y') }}</td>
                <td>{{ $e->category }}</td>
                <td>{{ $e->description }}</td>
                <td>৳{{ number_format($e->amount, 2) }}</td>
                <td>{{ ucfirst($e->payment_method) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td>৳{{ number_format($total, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <div class="footer">Unity Club &nbsp;|&nbsp; Confidential financial information</div>
</body>
</html>
