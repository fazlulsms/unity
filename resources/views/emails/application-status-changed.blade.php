@extends('emails.layout')
@php
    $isRejected  = $status === 'rejected';
    $isMoreInfo  = $status === 'more_info_required';
    $isPhotoReq  = $status === 'photo_required';

    if ($isRejected) {
        $heading  = 'Application Update';
        $subhead  = 'We were unable to approve your application at this time.';
        $badgeBg  = '#fef2f2'; $badgeColor = '#dc2626'; $badgeText = 'Not Approved';
    } elseif ($isMoreInfo) {
        $heading  = 'Additional Information Required';
        $subhead  = 'We need more information to process your application.';
        $badgeBg  = '#fef3c7'; $badgeColor = '#d97706'; $badgeText = 'Action Required';
    } else {
        $heading  = 'Photo Required';
        $subhead  = 'Please submit a photo to proceed with your application.';
        $badgeBg  = '#fef3c7'; $badgeColor = '#d97706'; $badgeText = 'Action Required';
    }
@endphp
@section('email-content')

<h2 style="margin:0 0 6px;color:#0f172a;font-size:22px;font-weight:700;">{{ $heading }}</h2>
<p style="margin:0 0 28px;color:#64748b;font-size:14px;">{{ $subhead }}</p>

<p style="margin:0 0 16px;color:#334155;font-size:15px;">Dear <strong>{{ $application->full_name }}</strong>,</p>

@if($isRejected)
<p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
    After careful review, we regret to inform you that your membership application to <strong>Unity Circle</strong>
    has not been approved at this time.
</p>
@elseif($isMoreInfo)
<p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
    Thank you for applying to <strong>Unity Circle</strong>. Our team has reviewed your application and requires some additional information before we can proceed.
</p>
@else
<p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
    Thank you for applying to <strong>Unity Circle</strong>. To complete your application review, we require a recent photograph.
</p>
@endif

{{-- Status badge --}}
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
<tr>
    <td style="background:{{ $badgeBg }};border-radius:8px;border:1px solid {{ $isRejected ? '#fecaca' : '#fde68a' }};padding:14px 20px;">
        <p style="margin:0;font-size:13px;color:{{ $badgeColor }};font-weight:700;">Application Status: <span>{{ $badgeText }}</span></p>
    </td>
</tr>
</table>

@if($remark)
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:24px;">
<tr><td style="padding:20px 24px;">
    <p style="margin:0 0 8px;color:#475569;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">
        @if($isRejected) Reason @elseif($isMoreInfo) Information Required @else Required Action @endif
    </p>
    <p style="margin:0;color:#334155;font-size:14px;line-height:1.7;">{{ $remark }}</p>
</td></tr>
</table>
@endif

@if($isMoreInfo || $isPhotoReq)
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f0f9ff;border-radius:8px;border:1px solid #bae6fd;margin-bottom:24px;">
<tr><td style="padding:16px 20px;">
    <p style="margin:0;color:#0369a1;font-size:13px;line-height:1.6;">
        @if($isMoreInfo)
            Please contact us or reply with the requested information so we can continue reviewing your application.
        @else
            Please submit a clear, recent photograph so we can complete your application review.
        @endif
    </p>
</td></tr>
</table>
@endif

@if($isRejected)
<p style="margin:0 0 16px;color:#475569;font-size:14px;line-height:1.7;">
    If you believe this decision was made in error, or if you would like to discuss your application, please contact us directly.
</p>
@endif

<p style="margin:0;color:#475569;font-size:14px;">
    Regards,<br>
    <strong>Unity Circle Administration</strong>
</p>

@endsection
