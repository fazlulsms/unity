<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyFeeSubmission extends Model
{
    protected $fillable = [
        'member_id', 'user_id', 'month', 'year', 'amount', 'payment_date',
        'payment_method', 'transaction_reference', 'proof_attachment', 'notes',
        'status', 'rejection_reason', 'approved_by', 'approved_at',
        'approval_remarks', 'receipt_id', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'approved_at' => 'datetime',
            'amount' => 'decimal:2',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'monthly_fee_submission_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_attachment ? url('uploads/' . $this->proof_attachment) : null;
    }
}
