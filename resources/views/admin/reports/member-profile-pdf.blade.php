<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Member Profile – {{ $member->member_number }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; background: #fff; }
    .page { padding: 32px 36px; }

    /* Header bar */
    .org-header { background: #1d4ed8; color: #fff; padding: 18px 24px; border-radius: 4px; margin-bottom: 24px; }
    .org-header-inner { display: table; width: 100%; }
    .org-text { display: table-cell; vertical-align: middle; }
    .org-name  { font-size: 22px; font-weight: bold; letter-spacing: 1px; }
    .doc-title { font-size: 12px; opacity: 0.85; margin-top: 3px; }

    /* Photo + identity */
    .profile-top { display: table; width: 100%; margin-bottom: 20px; }
    .photo-cell  { display: table-cell; width: 110px; vertical-align: top; }
    .photo-cell img { width: 100px; height: 110px; object-fit: cover; border: 3px solid #1d4ed8; border-radius: 4px; }
    .photo-placeholder { width: 100px; height: 110px; background: #dbeafe; border: 3px solid #1d4ed8; border-radius: 4px;
                          text-align: center; line-height: 110px; font-size: 36px; color: #1d4ed8; }
    .identity-cell { display: table-cell; vertical-align: top; padding-left: 16px; }
    .member-name { font-size: 18px; font-weight: bold; color: #1d4ed8; margin-bottom: 4px; }
    .member-id   { display: inline-block; background: #1d4ed8; color: #fff; font-size: 10px; font-weight: bold;
                   padding: 3px 10px; border-radius: 3px; letter-spacing: 0.5px; margin-bottom: 8px; }
    .id-row { margin-bottom: 4px; }
    .id-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; }
    .id-value { font-size: 11px; color: #111827; font-weight: bold; margin-top: 1px; }
    .status-badge { display: inline-block; padding: 3px 10px; border-radius: 10px; font-size: 9px; font-weight: bold; }
    .status-active   { background: #d1fae5; color: #065f46; }
    .status-inactive { background: #f3f4f6; color: #374151; }
    .status-suspended { background: #fee2e2; color: #991b1b; }

    /* Section */
    .section { margin-bottom: 18px; }
    .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;
                     color: #1d4ed8; border-bottom: 1.5px solid #bfdbfe; padding-bottom: 4px; margin-bottom: 10px; }
    .field-grid { display: table; width: 100%; border-collapse: collapse; }
    .field-row  { display: table-row; }
    .field-cell { display: table-cell; width: 50%; padding: 4px 8px 4px 0; vertical-align: top; }
    .field-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; }
    .field-value { font-size: 11px; color: #111827; font-weight: 600; margin-top: 2px; }

    /* Financial highlight */
    .fin-table { display: table; width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 18px; }
    .fin-cell  { display: table-cell; border: 1px solid #e5e7eb; border-radius: 4px; padding: 10px; text-align: center; }
    .fin-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; }
    .fin-value { font-size: 15px; font-weight: bold; margin-top: 4px; }

    /* Footer */
    .footer { margin-top: 28px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    .footer-note { text-align: center; font-size: 9px; color: #9ca3af; }
    .footer-seal { text-align: right; margin-top: 12px; }
    .seal-line { border-top: 1px solid #9ca3af; display: inline-block; width: 160px; margin-top: 30px; }
    .seal-label { font-size: 9px; color: #6b7280; }
</style>
</head>
<body>
<div class="page">

    {{-- Organization header --}}
    <div class="org-header">
        <div class="org-header-inner">
            <div class="org-text">
                <div class="org-name">Unity Circle</div>
                <div class="doc-title">Formal Member Profile</div>
            </div>
        </div>
    </div>

    {{-- Photo + identity --}}
    <div class="profile-top">
        <div class="photo-cell">
            @if($photoData)
                <img src="{{ $photoData }}" alt="Member Photo">
            @else
                <div class="photo-placeholder">👤</div>
            @endif
        </div>
        <div class="identity-cell">
            <div class="member-name">{{ $member->user->name }}</div>
            <div class="member-id">{{ $member->member_number }}</div>
            <div class="id-row" style="margin-top:6px;">
                <div class="id-label">Status</div>
                <div class="id-value">
                    <span class="status-badge status-{{ $member->status }}">{{ ucfirst($member->status) }}</span>
                </div>
            </div>
            <div class="id-row" style="margin-top:6px;">
                <div class="id-label">Member Since</div>
                <div class="id-value">{{ $member->join_date->format('d M Y') }}</div>
            </div>
            <div class="id-row" style="margin-top:6px;">
                <div class="id-label">Monthly Contribution</div>
                <div class="id-value" style="color:#1d4ed8;">৳ {{ number_format($member->monthly_fee_amount, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Contact Information --}}
    <div class="section">
        <div class="section-title">Contact Information</div>
        <div class="field-grid">
            <div class="field-row">
                <div class="field-cell">
                    <div class="field-label">Phone</div>
                    <div class="field-value">{{ $member->user->phone ?? '—' }}</div>
                </div>
                <div class="field-cell">
                    <div class="field-label">Email</div>
                    <div class="field-value">{{ str_contains($member->user->email ?? '', '@unity.local') ? '—' : ($member->user->email ?? '—') }}</div>
                </div>
            </div>
            <div class="field-row">
                <div class="field-cell" colspan="2">
                    <div class="field-label">Address</div>
                    <div class="field-value">{{ $member->user->address ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Personal Details --}}
    <div class="section">
        <div class="section-title">Personal Details</div>
        <div class="field-grid">
            <div class="field-row">
                <div class="field-cell">
                    <div class="field-label">Date of Birth</div>
                    <div class="field-value">{{ $member->user->date_of_birth?->format('d M Y') ?? '—' }}</div>
                </div>
                <div class="field-cell">
                    <div class="field-label">Profession</div>
                    <div class="field-value">{{ $member->user->profession ?? '—' }}</div>
                </div>
            </div>
            <div class="field-row">
                <div class="field-cell">
                    <div class="field-label">Emergency Contact</div>
                    <div class="field-value">{{ $member->user->emergency_contact ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Nominee Details --}}
    <div class="section">
        <div class="section-title">Nominee Details</div>
        <div class="field-grid">
            <div class="field-row">
                <div class="field-cell">
                    <div class="field-label">Nominee Name</div>
                    <div class="field-value">{{ $member->user->nominee_name ?? '—' }}</div>
                </div>
                <div class="field-cell">
                    <div class="field-label">Nominee Contact</div>
                    <div class="field-value">{{ $member->user->nominee_contact ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial summary --}}
    <div class="fin-table">
        <div class="fin-cell">
            <div class="fin-label">Monthly Fee</div>
            <div class="fin-value" style="color:#1d4ed8;">৳ {{ number_format($member->monthly_fee_amount, 2) }}</div>
        </div>
        <div class="fin-cell">
            <div class="fin-label">Total Paid</div>
            <div class="fin-value" style="color:#059669;">৳ {{ number_format($member->total_paid, 2) }}</div>
        </div>
        <div class="fin-cell">
            <div class="fin-label">Outstanding</div>
            <div class="fin-value" style="color:{{ $member->total_due > 0 ? '#dc2626' : '#9ca3af' }};">৳ {{ number_format($member->total_due, 2) }}</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-note">
            Unity Circle · Formal Member Profile · Generated on {{ now()->format('d M Y \a\t H:i') }}<br>
            This is a computer-generated document and does not require a physical signature.
        </div>
        <div class="footer-seal" style="margin-top:20px;">
            <div class="seal-label" style="text-align:right;">Authorized by</div>
            <div class="seal-line"></div>
            <div class="seal-label" style="text-align:right;">Unity Circle Administration</div>
        </div>
    </div>

</div>
</body>
</html>
