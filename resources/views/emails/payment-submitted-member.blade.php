@extends('emails.layout')
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:22px;font-weight:700;">Payment Submission Received</h2>
<p style="margin:0 0 28px;color:#64748b;font-size:14px;">Your payment is pending admin approval.</p>

<p style="margin:0 0 16px;color:#334155;font-size:15px;">Dear <strong>{{ $submission->member->user->name }}</strong>,</p>

<p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
    We have received your monthly fee submission for <strong>{{ date('F', mktime(0,0,0,$submission->month,1)) }} {{ $submission->year }}</strong>.
    It is now pending review by our admin/treasurer team.
</p>

{{-- Submission details --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:22px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 14px;color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Submission Details</p>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;width:45%;">For Period</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ date('F', mktime(0,0,0,$submission->month,1)) }} {{ $submission->year }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Amount</td>
            <td style="color:#0f172a;font-size:13px;font-weight:700;padding:5px 0;border-bottom:1px solid #f1f5f9;">৳{{ number_format($submission->amount, 2) }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Payment Method</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ ucfirst($submission->payment_method) }}</td>
        </tr>
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Payment Date</td>
            <td style="color:#0f172a;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $submission->payment_date->format('d F Y') }}</td>
        </tr>
        @if($submission->transaction_reference)
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;border-bottom:1px solid #f1f5f9;">Transaction Ref.</td>
            <td style="color:#0f172a;font-size:13px;font-family:monospace;padding:5px 0;border-bottom:1px solid #f1f5f9;">{{ $submission->transaction_reference }}</td>
        </tr>
        @endif
        <tr>
            <td style="color:#64748b;font-size:13px;padding:5px 0;">Status</td>
            <td style="padding:5px 0;">
                <span style="background:#fef3c7;color:#92400e;font-size:12px;font-weight:600;padding:2px 10px;border-radius:20px;">Pending Approval</span>
            </td>
        </tr>
    </table>
</td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0f9ff;border-radius:8px;border:1px solid #bae6fd;margin-bottom:24px;">
<tr><td style="padding:14px 18px;">
    <p style="margin:0;color:#0369a1;font-size:13px;line-height:1.6;">You will receive another email once your payment has been reviewed. Upon approval, a receipt will be generated and sent to you.</p>
</td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;">
<tr>
    <td style="background:#2563eb;border-radius:8px;">
        <a href="{{ url('/member/fees') }}" style="display:inline-block;padding:11px 28px;color:white;text-decoration:none;font-size:14px;font-weight:700;">View My Payments →</a>
    </td>
</tr>
</table>

@endsection
