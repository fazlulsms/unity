@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:20px;font-weight:700;">Your Login Access Link</h2>
<p style="margin:0 0 24px;color:#64748b;font-size:14px;">Unity Circle Member Portal</p>

<p style="margin:0 0 16px;color:#334155;font-size:15px;">Dear <strong>{{ $user->name }}</strong>,</p>

<p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
    Your administrator has sent you a secure link to access your Unity Circle member account.
    Click the button below to set or reset your password and sign in.
</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fef3c7;border-radius:8px;border:1px solid #fde68a;margin-bottom:24px;">
<tr><td style="padding:14px 18px;">
    <p style="margin:0;color:#92400e;font-size:13px;line-height:1.6;">
        <strong>This link expires in 24 hours.</strong> If it has expired, use <em>Forgot Password</em> on the login page to request a new one.
    </p>
</td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
<tr>
    <td style="background:#2563eb;border-radius:8px;text-align:center;">
        <a href="{{ $setupUrl }}" style="display:inline-block;padding:13px 36px;color:white;text-decoration:none;font-size:15px;font-weight:700;">
            Access My Account →
        </a>
    </td>
</tr>
</table>

<p style="margin:0 0 10px;color:#475569;font-size:14px;">Your login email is:</p>
<p style="margin:0 0 24px;font-size:14px;font-weight:700;color:#0f172a;font-family:monospace;">{{ $user->email }}</p>

<p style="margin:0;color:#94a3b8;font-size:13px;">
    If you did not expect this email, please ignore it or contact your club administrator.
</p>
@endsection
