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
        'review_remarks', 'user_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'membership_date' => 'date',
            'reviewed_at' => 'datetime',
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

    public function isPending(): bool
    {
        return $this->status === 'pending';
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

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'  => '<span class="badge badge-warning">Pending</span>',
            'approved' => '<span class="badge badge-success">Approved</span>',
            'rejected' => '<span class="badge badge-danger">Rejected</span>',
            default    => '<span class="badge badge-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
