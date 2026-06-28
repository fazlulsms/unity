<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoosterContribution extends Model
{
    protected $fillable = [
        'title', 'period_date', 'expected_amount_per_member',
        'status', 'remarks', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'period_date'                => 'date',
            'expected_amount_per_member' => 'decimal:2',
        ];
    }

    // ── Relationships ────────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'booster_contribution_member')
            ->withPivot('expected_amount', 'remarks')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(BoosterPayment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // ── Aggregates (based only on actual member assignments + payments) ───────

    public function getTotalExpectedAttribute(): float
    {
        return (float) $this->members()->sum('expected_amount');
    }

    public function getTotalDepositedAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getTotalDueAttribute(): float
    {
        return max(0.0, $this->total_expected - $this->total_deposited);
    }

    /** Amount a given member has paid toward this drive. */
    public function depositedForMember(int $memberId): float
    {
        return (float) $this->payments()->where('member_id', $memberId)->sum('amount');
    }
}
