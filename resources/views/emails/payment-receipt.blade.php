<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 14px; color: #333; background: #f4f4f4; margin: 0; padding: 20px; }
    .card { background: #fff; max-width: 560px; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .header { background: #1a7a4a; color: #fff; padding: 24px 32px; }
    .header h1 { margin: 0; font-size: 20px; }
    .header p { margin: 4px 0 0; font-size: 12px; opacity: .8; }
    .body { padding: 28px 32px; }
    .body p { line-height: 1.6; margin: 0 0 12px; }
    .receipt-box { background: #f0faf4; border: 1px solid #a7d7b7; border-radius: 6px; padding: 18px 22px; margin: 18px 0; }
    .receipt-box table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .receipt-box td { padding: 5px 0; }
    .receipt-box td:first-child { color: #555; width: 50%; }
    .receipt-box td:last-child { font-weight: bold; text-align: right; }
    .amount { font-size: 22px; font-weight: bold; color: #1a7a4a; text-align: center; margin: 12px 0; }
    .footer { background: #f9f9f9; border-top: 1px solid #eee; padding: 14px 32px; font-size: 11px; color: #888; }
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>Payment Receipt</h1>
        <p>{{ $receipt->receipt_number }}</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $receipt->member_name }}</strong>,</p>
        <p>Your monthly fee payment has been approved. Please find your receipt details below.</p>

        <div class="amount">৳{{ number_format($receipt->amount, 2) }}</div>

        <div class="receipt-box">
            <table>
                <tr>
                    <td>Receipt Number</td>
                    <td>{{ $receipt->receipt_number }}</td>
                </tr>
                <tr>
                    <td>For Period</td>
                    <td>{{ date('F', mktime(0,0,0,$receipt->month,1)) }} {{ $receipt->year }}</td>
                </tr>
                <tr>
                    <td>Payment Method</td>
                    <td>{{ ucfirst($receipt->payment_method) }}</td>
                </tr>
                <tr>
                    <td>Payment Date</td>
                    <td>{{ $receipt->payment_date ? \Carbon\Carbon::parse($receipt->payment_date)->format('d F Y') : '—' }}</td>
                </tr>
                <tr>
                    <td>Approved Date</td>
                    <td>{{ $receipt->approved_date ? \Carbon\Carbon::parse($receipt->approved_date)->format('d F Y') : '—' }}</td>
                </tr>
                <tr>
                    <td>Authorized By</td>
                    <td>{{ $receipt->authorized_by }}</td>
                </tr>
            </table>
        </div>

        <p>You can also download this receipt from the member portal under <em>Payment History</em>.</p>
        <p>Thank you for your timely payment!</p>
        <p>Regards,<br><strong>Unity Club Treasurer</strong></p>
    </div>
    <div class="footer">This is an automated email. Please do not reply directly to this message.</div>
</div>
</body>
</html>
