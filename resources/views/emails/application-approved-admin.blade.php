@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:22px;font-weight:700;">Application Approved</h2>
<p style="margin:0 0 28px;color:#64748b;font-size:14px;">A membership application has been approved and a member account created.</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0fdf4;border-radius:8px;border:1px solid #86efac;margin-bottom:24px;">
<tr><td style="padding:14px 18px;">
    <p style="margin:0;color:#166534;font-size:14px;font-weight:600;">New member record created successfully.</p>
</td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 14px;color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">New Member Details</p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;width:45%;">Member Name</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $member->user->name }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Member ID</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;font-family:monospace;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $member->member_number }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Login Email</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $member->user->email }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Monthly Fee</td>
            <td style="color:#0f172a;font-size:13px;font-weight:600;padding:5px 0;border-bottom:1px solid #f1f5f9;">৳{{ number_format($member->monthly_fee_amount, 0) }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;">Join Date</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;">{{ $member->join_date->format('d F Y') }}</td>
        </tr>
    </table>
</td></tr>
</table>

<p style="margin:0 0 8px;color:#475569;font-size:13px;">The welcome email with login credentials has been sent to the member.</p>

<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
<tr>
    <td style="background:#2563eb;border-radius:8px;">
        <a href="{{ url('/admin/members/' . $member->id) }}" style="display:inline-block;padding:11px 28px;color:white;text-decoration:none;font-size:14px;font-weight:700;">View Member Profile →</a>
    </td>
</tr>
</table>

<p style="margin:0;color:#94a3b8;font-size:13px;">Approved by: {{ $approvedBy }}</p>

@endsection
