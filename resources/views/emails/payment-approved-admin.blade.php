@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:22px;font-weight:700;">Payment Approved</h2>
<p style="margin:0 0 28px;color:#64748b;font-size:14px;">A payment has been approved and receipt generated.</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0fdf4;border-radius:8px;border:1px solid #86efac;margin-bottom:24px;">
<tr><td style="padding:14px 18px;">
    <p style="margin:0;color:#166534;font-size:14px;font-weight:600;">Receipt {{ $receipt->receipt_number }} has been generated and sent to the member.</p>
</td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:20px 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;width:45%;">Member</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $receipt->member_name }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Receipt No.</td>
            <td style="color:#0f172a;font-size:13px;font-family:monospace;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $receipt->receipt_number }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Period</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ date('F', mktime(0,0,0,$receipt->month,1)) }} {{ $receipt->year }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Amount</td>
            <td style="color:#0f172a;font-size:14px;font-weight:800;padding:5px 0;border-bottom:1px solid #f1f5f9;">৳{{ number_format($receipt->amount, 2) }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;">Approved By</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;">{{ $receipt->authorized_by }}</td>
        </tr>
    </table>
</td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0">
<tr>
    <td style="background:#2563eb;border-radius:8px;">
        <a href="{{ url('/admin/payments') }}" style="display:inline-block;padding:11px 28px;color:white;text-decoration:none;font-size:14px;font-weight:700;">View All Payments →</a>
    </td>
</tr>
</table>

@endsection
