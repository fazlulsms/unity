@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:22px;font-weight:700;">New Payment Awaiting Approval</h2>
<p style="margin:0 0 28px;color:#64748b;font-size:14px;">A member has submitted a fee payment for review.</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fef3c7;border-radius:8px;border:1px solid #fde68a;margin-bottom:24px;">
<tr><td style="padding:14px 18px;">
    <p style="margin:0;color:#92400e;font-size:14px;font-weight:600;">Action Required: Please review and approve or reject this payment.</p>
</td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 14px;color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Payment Details</p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;width:45%;">Member Name</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $submission->member->user->name }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Member ID</td>
            <td style="color:#0f172a;font-size:13px;font-family:monospace;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $submission->member->member_number }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">For Period</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ date('F', mktime(0,0,0,$submission->month,1)) }} {{ $submission->year }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Amount</td>
            <td style="color:#0f172a;font-size:14px;font-weight:800;padding:5px 0;border-bottom:1px solid #f1f5f9;">৳{{ number_format($submission->amount, 2) }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Method</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ ucfirst($submission->payment_method) }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;">Payment Date</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;">{{ $submission->payment_date->format('d F Y') }}</td>
        </tr>
    </table>
</td></tr>
</table>

@if($submission->notes)
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:16px 20px;">
    <p style="margin:0 0 6px;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase;">Member Notes</p>
    <p style="margin:0;color:#475569;font-size:13px;">{{ $submission->notes }}</p>
</td></tr>
</table>
@endif

<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
<tr>
    <td style="background:#2563eb;border-radius:8px;">
        <a href="{{ url('/admin/payments/' . $submission->id) }}" style="display:inline-block;padding:12px 32px;color:white;text-decoration:none;font-size:14px;font-weight:700;">Review Payment →</a>
    </td>
</tr>
</table>

@endsection
