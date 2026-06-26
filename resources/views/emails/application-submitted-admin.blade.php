@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:22px;font-weight:700;">New Membership Application</h2>
<p style="margin:0 0 28px;color:#64748b;font-size:14px;">A new application requires your review.</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#fef3c7;border-radius:8px;border:1px solid #fde68a;margin-bottom:24px;">
<tr><td style="padding:14px 18px;">
    <p style="margin:0;color:#92400e;font-size:14px;font-weight:600;">Action Required: Please review this application in the admin panel.</p>
</td></tr>
</table>

{{-- Applicant details --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 14px;color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Applicant Information</p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;width:45%;">Full Name</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $application->full_name }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Phone</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $application->phone }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Email</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $application->email ?? '(not provided)' }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Address</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $application->address }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Requested Monthly Fee</td>
            <td style="color:#0f172a;font-size:13px;font-weight:600;padding:5px 0;border-bottom:1px solid #f1f5f9;">৳{{ number_format($application->monthly_fee_amount, 0) }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;">Submitted</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;">{{ $application->created_at->format('d F Y, h:i A') }}</td>
        </tr>
    </table>
</td></tr>
</table>

@if($application->notes)
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:16px 20px;">
    <p style="margin:0 0 6px;color:#64748b;font-size:11px;font-weight:700;text-transform:uppercase;">Applicant Notes</p>
    <p style="margin:0;color:#475569;font-size:13px;line-height:1.6;">{{ $application->notes }}</p>
</td></tr>
</table>
@endif

{{-- CTA button --}}
<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
<tr>
    <td style="background:#2563eb;border-radius:8px;">
        <a href="{{ url('/admin/applications/' . $application->id) }}" style="display:inline-block;padding:12px 32px;color:white;text-decoration:none;font-size:14px;font-weight:700;">Review Application →</a>
    </td>
</tr>
</table>

<p style="margin:0;color:#94a3b8;font-size:13px;">Log in to the admin panel to approve, reject, or request additional information.</p>

@endsection
