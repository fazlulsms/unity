<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipApplication extends Model
{
    protected $fillable = [
        'full_name', 'photo', 'phone', 'email', 'address', 'date_of_birth',
        'profession', 'emergency_contact', 'nominee_name', 'nominee_contact',
        'is_existing_member', 'membership_date', 'monthly_fee_amount', 'notes',
        'status', 'rejection_reason', 'reviewed_by', 'reviewed_at',
        'review_remarks', 'internal_notes', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'      => 'date',
            'membership_date'    => 'date',
            'reviewed_at'        => 'datetime',
            'is_existing_member' => 'boolean',
            'monthly_fee_amount' => 'decimal:2',
        ];
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'application_id');
    }

    // True for any status that can still be acted upon (not yet approved/rejected)
    public function isOpen(): bool
    {
        return in_array($this->status, ['pending', 'under_review', 'more_info_required', 'photo_required']);
    }

    // Kept for backward compatibility — views use isPending() to show action forms
    public function isPending(): bool
    {
        return $this->isOpen();
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'            => 'Pending',
            'under_review'       => 'Under Review',
            'more_info_required' => 'More Info Required',
            'photo_required'     => 'Photo Required',
            'approved'           => 'Approved',
            'rejected'           => 'Rejected',
            default              => ucfirst($this->status),
        };
    }

    public function statusClass(): string
    {
        return match ($this->status) {
            'pending'            => 'badge-pending',
            'under_review'       => 'badge-under-review',
            'more_info_required' => 'badge-more-info',
            'photo_required'     => 'badge-photo-required',
            'approved'           => 'badge-approved',
            'rejected'           => 'badge-rejected',
            default              => 'badge',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return '<span class="' . $this->statusClass() . '">' . $this->statusLabel() . '</span>';
    }
}
