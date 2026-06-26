@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:22px;font-weight:700;">Application Received</h2>
<p style="margin:0 0 28px;color:#64748b;font-size:14px;">Your membership application is under review.</p>

<p style="margin:0 0 16px;color:#334155;font-size:15px;">Dear <strong>{{ $application->full_name }}</strong>,</p>

<p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
    Thank you for applying to join <strong>Unity Circle</strong>! We have successfully received your membership application and it is currently being reviewed by our team.
</p>

{{-- Application details --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 14px;color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Application Summary</p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;width:45%;">Applicant Name</td>
            <td style="color:#0f172a;font-size:13px;font-weight:600;padding:4px 0;">{{ $application->full_name }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;">Phone</td>
            <td style="color:#0f172a;font-size:13px;padding:4px 0;">{{ $application->phone }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;">Submitted On</td>
            <td style="color:#0f172a;font-size:13px;padding:4px 0;">{{ $application->created_at->format('d F Y, h:i A') }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:4px 0;">Status</td>
            <td style="padding:4px 0;">
                <span style="background:#fef3c7;color:#92400e;font-size:12px;font-weight:600;padding:2px 10px;border-radius:20px;">Pending Review</span>
            </td>
        </tr>
    </table>
</td></tr>
</table>

{{-- What happens next --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0f9ff;border-radius:10px;border:1px solid #bae6fd;margin-bottom:24px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 12px;color:#0369a1;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">What Happens Next</p>
    <ol style="margin:0;padding-left:18px;color:#475569;font-size:13px;line-height:2.2;">
        <li>Our admin team will review your application</li>
        <li>We may contact you if additional information is required</li>
        <li>You will receive a confirmation email once a decision is made</li>
        <li>If approved, you will receive your member login credentials</li>
    </ol>
</td></tr>
</table>

<p style="margin:0;color:#475569;font-size:14px;line-height:1.7;">
    If you have any questions about your application, please contact us directly.
</p>

<p style="margin:20px 0 0;color:#475569;font-size:14px;">
    Warm regards,<br>
    <strong>Unity Circle Administration</strong>
</p>

@endsection
