<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FdrRecord extends Model
{
    protected $fillable = [
        'bank_name', 'branch', 'fdr_number', 'opening_date', 'maturity_date',
        'principal_amount', 'interest_rate', 'expected_maturity_amount',
        'interest_received', 'status', 'is_public_reference', 'notes',
        'attachment', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'opening_date' => 'date',
            'maturity_date' => 'date',
            'principal_amount' => 'decimal:2',
            'expected_maturity_amount' => 'decimal:2',
            'interest_received' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'is_public_reference' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? url('uploads/' . $this->attachment) : null;
    }

    public function getDaysToMaturityAttribute(): int
    {
        if ($this->status === 'active') {
            return (int) now()->diffInDays($this->maturity_date, false);
        }
        return 0;
    }
}
