@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:22px;font-weight:700;">Welcome to Unity Circle!</h2>
<p style="margin:0 0 28px;color:#64748b;font-size:14px;">Your membership application has been approved.</p>

<p style="margin:0 0 16px;color:#334155;font-size:15px;">Dear <strong>{{ $user->name }}</strong>,</p>

<p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
    Congratulations! Your application to join <strong>Unity Circle</strong> has been reviewed and approved.
    Please use the button below to set up your password and activate your member account.
</p>

{{-- Member info card --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0f9ff;border-radius:10px;border:1px solid #bae6fd;margin-bottom:22px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 14px;color:#0369a1;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Your Member Details</p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;width:45%;">Member ID</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;font-family:monospace;padding:4px 0;">{{ $member->member_number }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;">Login Email</td>
            <td style="color:#0f172a;font-size:13px;padding:4px 0;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;">Monthly Contribution</td>
            <td style="color:#0f172a;font-size:13px;font-weight:600;padding:4px 0;">৳{{ number_format($member->monthly_fee_amount, 0) }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;">Member Since</td>
            <td style="color:#0f172a;font-size:13px;padding:4px 0;">{{ $member->join_date->format('d F Y') }}</td>
        </tr>
    </table>
</td></tr>
</table>

{{-- Security notice --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fef3c7;border-radius:8px;border:1px solid #fde68a;margin-bottom:24px;">
<tr><td style="padding:14px 18px;">
    <p style="margin:0;color:#92400e;font-size:13px;line-height:1.6;">
        <strong>This link expires in 24 hours.</strong> Click the button below to set your password.
        If the link expires, use <em>Forgot Password</em> on the login page to get a new one.
    </p>
</td></tr>
</table>

{{-- CTA button --}}
<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
<tr>
    <td style="background:#2563eb;border-radius:8px;text-align:center;">
        <a href="{{ $setupUrl }}" style="display:inline-block;padding:13px 36px;color:white;text-decoration:none;font-size:15px;font-weight:700;">
            Set Up My Account →
        </a>
    </td>
</tr>
</table>

<p style="margin:0 0 10px;color:#475569;font-size:14px;">From the member portal you can:</p>
<ul style="margin:0 0 24px;padding-left:20px;color:#475569;font-size:13px;line-height:2.2;">
    <li>Complete and update your profile</li>
    <li>Submit monthly fee payments</li>
    <li>View and download payment receipts</li>
    <li>Read club notices and announcements</li>
    <li>Access financial transparency reports</li>
</ul>

<p style="margin:0;color:#94a3b8;font-size:13px;">If you need assistance, please contact your club administrator.</p>

@endsection
