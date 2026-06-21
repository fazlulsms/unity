<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 14px; color: #333; background: #f4f4f4; margin: 0; padding: 20px; }
    .card { background: #fff; max-width: 560px; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .header { background: #1e3a5f; color: #fff; padding: 24px 32px; }
    .header h1 { margin: 0; font-size: 20px; }
    .body { padding: 28px 32px; }
    .body p { line-height: 1.6; margin: 0 0 12px; }
    .info-box { background: #f0f4f9; border-left: 4px solid #1e3a5f; padding: 14px 18px; border-radius: 4px; margin: 18px 0; }
    .info-box p { margin: 4px 0; font-size: 13px; }
    .info-box strong { display: inline-block; min-width: 130px; color: #555; }
    .footer { background: #f9f9f9; border-top: 1px solid #eee; padding: 14px 32px; font-size: 11px; color: #888; }
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>Welcome to Unity Circle!</h1>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $member->user->name }}</strong>,</p>
        <p>We are delighted to welcome you as an official member of <strong>Unity Circle</strong>. Your membership application has been approved and your account is now active.</p>

        <div class="info-box">
            <p><strong>Member Number:</strong> {{ $member->member_number }}</p>
            <p><strong>Email (login):</strong> {{ $member->user->email }}</p>
            <p><strong>Temporary Password:</strong> {{ $password }}</p>
            <p><strong>Monthly Fee:</strong> ৳{{ number_format($member->monthly_fee_amount, 2) }}</p>
            <p><strong>Join Date:</strong> {{ $member->join_date->format('d F Y') }}</p>
        </div>

        <p>Please log in to the member portal and change your password immediately.</p>
        <p>You can submit your monthly fee payments through the portal, and our team will approve them and send you a receipt.</p>
        <p>If you have any questions, feel free to contact us.</p>
        <p>Warm regards,<br><strong>Unity Circle Administration</strong></p>
    </div>
    <div class="footer">This is an automated email. Please do not reply directly to this message.</div>
</div>
</body>
</html>
