<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt {{ $receipt->receipt_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 13px; color: #1e293b; background: #fff; }
    .page { padding: 40px 50px; }

    .header { text-align: center; margin-bottom: 28px; padding-bottom: 18px; border-bottom: 2px solid #16a34a; }
    .org-name { font-size: 22px; font-weight: 700; color: #15803d; margin-bottom: 4px; }
    .receipt-title { font-size: 11px; color: #64748b; letter-spacing: 2px; text-transform: uppercase; }
    .receipt-no { font-size: 11px; color: #94a3b8; margin-top: 5px; font-family: monospace; }

    .amount-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; text-align: center; padding: 18px; margin: 22px 0; }
    .amount-label { font-size: 10px; color: #16a34a; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; margin-bottom: 5px; }
    .amount-value { font-size: 30px; font-weight: 800; color: #15803d; }

    table.details { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    table.details td { padding: 9px 0; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
    table.details td.label { color: #64748b; width: 48%; }
    table.details td.value { color: #0f172a; font-weight: 600; text-align: right; }

    .footer { margin-top: 28px; padding-top: 14px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 11px; color: #94a3b8; line-height: 1.8; }

    .watermark { color: #16a34a; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; display: inline-block; border: 1px solid #86efac; padding: 2px 10px; border-radius: 4px; margin-top: 10px; }
</style>
</head>
<body>
<div class="page">

    <div class="header">
        <div class="org-name">Unity Circle</div>
        <div class="receipt-title">Official Payment Receipt</div>
        <div class="receipt-no">{{ $receipt->receipt_number }}</div>
    </div>

    <div class="amount-box">
        <div class="amount-label">Amount Paid</div>
        <div class="amount-value">&#2547;{{ number_format($receipt->amount, 2) }}</div>
    </div>

    <table class="details">
        <tr>
            <td class="label">Member Name</td>
            <td class="value">{{ $receipt->member_name }}</td>
        </tr>
        <tr>
            <td class="label">For Period</td>
            <td class="value">{{ \Carbon\Carbon::createFromDate(null, $receipt->month, 1)->format('F') }} {{ $receipt->year }}</td>
        </tr>
        <tr>
            <td class="label">Payment Method</td>
            <td class="value">{{ ucfirst($receipt->payment_method) }}</td>
        </tr>
        <tr>
            <td class="label">Payment Date</td>
            <td class="value">{{ $receipt->payment_date ? $receipt->payment_date->format('d F Y') : '—' }}</td>
        </tr>
        <tr>
            <td class="label">Approved Date</td>
            <td class="value">{{ $receipt->approved_date ? $receipt->approved_date->format('d F Y') : '—' }}</td>
        </tr>
        <tr>
            <td class="label">Authorized By</td>
            <td class="value">{{ $receipt->authorized_by }}</td>
        </tr>
        @if($receipt->submission?->transaction_reference)
        <tr>
            <td class="label">Transaction Reference</td>
            <td class="value" style="font-family: monospace; font-size: 12px;">{{ $receipt->submission->transaction_reference }}</td>
        </tr>
        @endif
    </table>

    <div class="footer">
        <div>This is a computer-generated receipt. No signature required.</div>
        <div>Unity Circle &bull; Generated {{ now()->format('d F Y, h:i A') }}</div>
        <div class="watermark">OFFICIAL RECEIPT</div>
    </div>

</div>
</body>
</html>
