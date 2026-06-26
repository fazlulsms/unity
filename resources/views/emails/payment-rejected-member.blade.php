@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:22px;font-weight:700;">Payment Not Approved</h2>
<p style="margin:0 0 28px;color:#64748b;font-size:14px;">Your payment submission requires correction.</p>

<p style="margin:0 0 16px;color:#334155;font-size:15px;">Dear <strong>{{ $submission->member->user->name }}</strong>,</p>

<p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
    We were unable to approve your monthly fee submission for
    <strong>{{ date('F', mktime(0,0,0,$submission->month,1)) }} {{ $submission->year }}</strong>.
    Please review the reason below and resubmit.
</p>

{{-- Rejection reason --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fef2f2;border-radius:10px;border:1px solid #fecaca;margin-bottom:22px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 8px;color:#dc2626;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Reason for Rejection</p>
    <p style="margin:0;color:#991b1b;font-size:14px;line-height:1.7;">{{ $submission->rejection_reason ?? 'No specific reason provided. Please contact the admin.' }}</p>
</td></tr>
</table>

{{-- Submission summary --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 12px;color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Submission That Was Rejected</p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;width:45%;">Period</td>
            <td style="color:#0f172a;font-size:13px;font-weight:600;padding:4px 0;">{{ date('F', mktime(0,0,0,$submission->month,1)) }} {{ $submission->year }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;">Amount</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;padding:4px 0;">৳{{ number_format($submission->amount, 2) }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;">Method</td>
            <td style="color:#0f172a;font-size:13px;padding:4px 0;">{{ ucfirst($submission->payment_method) }}</td>
        </tr>
    </table>
</td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0f9ff;border-radius:8px;border:1px solid #bae6fd;margin-bottom:24px;">
<tr><td style="padding:14px 18px;">
    <p style="margin:0;color:#0369a1;font-size:13px;line-height:1.6;">Please submit a new payment with the correct details. If you need assistance, contact the club administrator.</p>
</td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
<tr>
    <td style="background:#2563eb;border-radius:8px;">
        <a href="{{ url('/member/fees/submit') }}" style="display:inline-block;padding:11px 28px;color:white;text-decoration:none;font-size:14px;font-weight:700;">Submit New Payment →</a>
    </td>
</tr>
</table>

@endsection
