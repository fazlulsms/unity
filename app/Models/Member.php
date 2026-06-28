<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'user_id', 'application_id', 'member_number', 'join_date',
        'monthly_fee_amount', 'joining_contribution', 'status', 'notes', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'join_date'            => 'date',
            'monthly_fee_amount'   => 'decimal:2',
            'joining_contribution' => 'decimal:2',
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

    public function boosterContributions()
    {
        return $this->belongsToMany(BoosterContribution::class, 'booster_contribution_member')
            ->withPivot('expected_amount', 'remarks')
            ->withTimestamps();
    }

    public function boosterPayments()
    {
        return $this->hasMany(BoosterPayment::class);
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

    /**
     * Number of calendar months for which a fee is payable,
     * from the join month up to and including the current month.
     * Uses calendar arithmetic (year/month only) — never day-precise.
     */
    public function getPayableMonthsAttribute(): int
    {
        return max(1,
            (now()->year  - $this->join_date->year)  * 12
            + (now()->month - $this->join_date->month) + 1
        );
    }

    /** Monthly fee payable across all months (no joining contribution). */
    public function getMonthlyTotalPayableAttribute(): float
    {
        return $this->payable_months * (float) $this->monthly_fee_amount;
    }

    /** Grand total payable = joining contribution + all monthly fees. */
    public function getTotalPayableAttribute(): float
    {
        return $this->monthly_total_payable + (float) ($this->joining_contribution ?? 0);
    }

    /** Sum of all admin-approved payments. */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->approvedFeeSubmissions()->sum('amount');
    }

    /** Outstanding due = total payable − total paid (never negative). */
    public function getTotalDueAttribute(): float
    {
        return max(0.0, $this->total_payable - $this->total_paid);
    }

    // ── Booster contribution (treated as direct member contribution) ─────────

    /** Total booster amount expected from this member across all drives. */
    public function getBoosterExpectedAttribute(): float
    {
        return (float) $this->boosterContributions()->sum('expected_amount');
    }

    /** Total booster amount this member has actually paid. */
    public function getBoosterPaidAttribute(): float
    {
        return (float) $this->boosterPayments()->sum('amount');
    }

    /** Outstanding booster due (never negative). */
    public function getBoosterDueAttribute(): float
    {
        return max(0.0, $this->booster_expected - $this->booster_paid);
    }

    /** Monthly fees + booster, paid. */
    public function getTotalContributionPaidAttribute(): float
    {
        return $this->total_paid + $this->booster_paid;
    }

    /** Monthly fees + booster, expected. */
    public function getTotalContributionExpectedAttribute(): float
    {
        return $this->total_payable + $this->booster_expected;
    }

    /** Combined outstanding (monthly + booster). */
    public function getTotalContributionDueAttribute(): float
    {
        return $this->total_due + $this->booster_due;
    }
}
