<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'user_id', 'application_id', 'member_number', 'join_date',
        'monthly_fee_amount', 'status', 'notes', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'monthly_fee_amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(MembershipApplication::class, 'application_id');
    }

    public function feeSubmissions()
    {
        return $this->hasMany(MonthlyFeeSubmission::class);
    }

    public function approvedFeeSubmissions()
    {
        return $this->hasMany(MonthlyFeeSubmission::class)->where('status', 'approved');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function profileHistories()
    {
        return $this->hasMany(MemberProfileHistory::class);
    }

    public function additionalInfo()
    {
        return $this->hasOne(MemberAdditionalInfo::class);
    }

    public function familyMembers()
    {
        return $this->hasMany(MemberFamilyMember::class);
    }

    public function spouse()
    {
        return $this->hasOne(MemberFamilyMember::class)->where('type', 'spouse');
    }

    public function children()
    {
        return $this->hasMany(MemberFamilyMember::class)->where('type', 'child');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->approvedFeeSubmissions()->sum('amount');
    }

    public function getTotalDueAttribute(): float
    {
        $monthsSinceJoin = $this->join_date->diffInMonths(now()) + 1;
        $expected = $monthsSinceJoin * $this->monthly_fee_amount;
        return max(0, $expected - $this->total_paid);
    }
}
