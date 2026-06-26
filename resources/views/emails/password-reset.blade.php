@extends('emails.layout')

@section('email-content')
<h2 style="margin:0 0 16px; font-size:20px; font-weight:700; color:#0f172a;">Your Password Has Been Reset</h2>

<p style="margin:0 0 16px; color:#334155;">Hello <strong>{{ $user->name }}</strong>,</p>

<p style="margin:0 0 20px; color:#334155;">
    An administrator has reset your Unity Circle account password. Here is your new temporary password:
</p>

<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:20px; text-align:center; margin:0 0 24px;">
    <p style="margin:0 0 8px; font-size:13px; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; font-weight:600;">Temporary Password</p>
    <p style="margin:0; font-size:22px; font-weight:700; font-family:monospace; color:#dc2626; letter-spacing:2px;">{{ $password }}</p>
</div>

<div style="background:#fefce8; border:1px solid #fde047; border-radius:8px; padding:16px; margin:0 0 24px;">
    <p style="margin:0; font-size:14px; color:#713f12;">
        <strong>Important:</strong> You will be required to change this password immediately after logging in.
        Please choose a strong, unique password that you do not use elsewhere.
    </p>
</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tr>
        <td align="center">
            <a href="{{ url('/login') }}"
               style="display:inline-block; background:#2563eb; color:#ffffff; text-decoration:none;
                      font-size:15px; font-weight:600; padding:12px 32px; border-radius:8px;">
                Login to Your Account
            </a>
        </td>
    </tr>
</table>

<p style="margin:0; font-size:13px; color:#94a3b8;">
    If you did not expect this email, please contact your club administrator immediately.
</p>
@endsection
