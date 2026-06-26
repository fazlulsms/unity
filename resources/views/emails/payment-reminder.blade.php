@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:20px;font-weight:700;">Monthly Fee Reminder</h2>
<p style="margin:0 0 24px;color:#64748b;font-size:14px;">{{ now()->format('F Y') }}</p>

<p style="margin:0 0 16px;color:#334155;font-size:15px;">Dear <strong>{{ $member->user->name }}</strong>,</p>

<p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
    This is a friendly reminder that your monthly contribution to Unity Circle is due.
    Please submit your payment at your earliest convenience.
</p>

@if($adminMessage)
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0f9ff;border-radius:8px;border:1px solid #bae6fd;margin-bottom:20px;">
<tr><td style="padding:16px 20px;">
    <p style="margin:0 0 4px;color:#0369a1;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Message from Admin</p>
    <p style="margin:0;color:#1e40af;font-size:14px;">{{ $adminMessage }}</p>
</td></tr>
</table>
@endif

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 14px;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Your Account Summary</p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;width:50%;">Member ID</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;font-family:monospace;padding:4px 0;">{{ $member->member_number }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;">Monthly Contribution</td>
            <td style="color:#0f172a;font-size:13px;font-weight:600;padding:4px 0;">৳{{ number_format($member->monthly_fee_amount, 0) }}</td>
        </tr>
        @if($member->total_due > 0)
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;">Outstanding Balance</td>
            <td style="color:#dc2626;font-size:13px;font-weight:700;padding:4px 0;">৳{{ number_format($member->total_due, 0) }}</td>
        </tr>
        @endif
    </table>
</td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
<tr>
    <td style="background:#2563eb;border-radius:8px;text-align:center;">
        <a href="{{ url('/member/fees/submit') }}" style="display:inline-block;padding:12px 32px;color:white;text-decoration:none;font-size:14px;font-weight:700;">
            Submit Payment Now →
        </a>
    </td>
</tr>
</table>

<p style="margin:0;color:#94a3b8;font-size:13px;">Thank you for your continued support of Unity Circle.</p>
@endsection
