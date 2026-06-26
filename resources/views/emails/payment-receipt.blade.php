@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:22px;font-weight:700;">Payment Approved</h2>
<p style="margin:0 0 28px;color:#64748b;font-size:14px;">Your monthly fee payment has been approved.</p>

<p style="margin:0 0 16px;color:#334155;font-size:15px;">Dear <strong>{{ $receipt->member_name }}</strong>,</p>

<p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
    Great news! Your monthly contribution for <strong>{{ date('F', mktime(0,0,0,$receipt->month,1)) }} {{ $receipt->year }}</strong>
    has been approved. Your receipt is below.
</p>

{{-- Amount highlight --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
<tr>
    <td align="center" style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:20px;">
        <p style="margin:0 0 4px;color:#16a34a;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Amount Paid</p>
        <p style="margin:0;color:#15803d;font-size:32px;font-weight:800;">৳{{ number_format($receipt->amount, 2) }}</p>
    </td>
</tr>
</table>

{{-- Receipt details --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 14px;color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Receipt Details</p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;width:50%;">Receipt Number</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;font-family:monospace;padding:5px 0;border-bottom:1px solid #f1f5f9;text-align:right;">{{ $receipt->receipt_number }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">For Period</td>
            <td style="color:#0f172a;font-size:13px;font-weight:600;padding:5px 0;border-bottom:1px solid #f1f5f9;text-align:right;">{{ date('F', mktime(0,0,0,$receipt->month,1)) }} {{ $receipt->year }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Payment Method</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;text-align:right;">{{ ucfirst($receipt->payment_method) }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Payment Date</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;text-align:right;">{{ $receipt->payment_date ? \Carbon\Carbon::parse($receipt->payment_date)->format('d F Y') : '—' }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Approved Date</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;text-align:right;">{{ $receipt->approved_date ? \Carbon\Carbon::parse($receipt->approved_date)->format('d F Y') : '—' }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;">Authorized By</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;text-align:right;">{{ $receipt->authorized_by }}</td>
        </tr>
    </table>
</td></tr>
</table>

{{-- CTA button --}}
<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
<tr>
    <td style="background:#16a34a;border-radius:8px;">
        <a href="{{ url('/member/fees') }}" style="display:inline-block;padding:11px 28px;color:white;text-decoration:none;font-size:14px;font-weight:700;">View My Payment History →</a>
    </td>
</tr>
</table>

<p style="margin:0;color:#94a3b8;font-size:13px;">You can download this receipt from the member portal under <em>My Payments</em>.</p>
<p style="margin:12px 0 0;color:#94a3b8;font-size:12px;font-style:italic;">This is a computer-generated receipt.</p>

@endsection
