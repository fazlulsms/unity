<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FdrRecord extends Model
{
    protected $fillable = [
        'bank_account_id', 'bank_name', 'branch', 'fdr_number', 'opening_date', 'maturity_date',
        'closure_date', 'principal_amount', 'interest_rate',
        'expected_maturity_amount', 'actual_maturity_amount', 'principal_returned',
        'interest_received', 'tax_deduction', 'status', 'is_public_reference', 'notes',
        'attachment', 'closure_attachment', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'opening_date'             => 'date',
            'maturity_date'            => 'date',
            'closure_date'             => 'date',
            'principal_amount'         => 'decimal:2',
            'expected_maturity_amount' => 'decimal:2',
            'actual_maturity_amount'   => 'decimal:2',
            'principal_returned'       => 'decimal:2',
            'interest_received'        => 'decimal:2',
            'tax_deduction'            => 'decimal:2',
            'interest_rate'            => 'decimal:2',
            'is_public_reference'      => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    /**
     * Net interest realised after any tax / deduction.
     */
    public function getNetInterestAttribute(): float
    {
        return max(0, (float) $this->interest_received - (float) $this->tax_deduction);
    }

    public function linkedIncome()
    {
        return $this->hasOne(Income::class, 'fdr_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['closed', 'matured']);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? url('uploads/' . $this->attachment) : null;
    }

    public function getClosureAttachmentUrlAttribute(): ?string
    {
        return $this->closure_attachment ? url('uploads/' . $this->closure_attachment) : null;
    }

    public function getDaysToMaturityAttribute(): int
    {
        if ($this->status === 'active') {
            return (int) now()->diffInDays($this->maturity_date, false);
        }
        return 0;
    }

    public function syncLinkedIncome(int $userId): void
    {
        if (!$this->interest_received || (float) $this->interest_received <= 0) {
            if ($this->linkedIncome && $this->linkedIncome->source_module === 'fdr') {
                $this->linkedIncome->update(['status' => 'voided', 'updated_by' => $userId]);
            }
            return;
        }

        $date = $this->closure_date ?? $this->maturity_date;

        $incomeData = [
            'income_type'   => 'fdr_interest',
            'source'        => $this->bank_name . ($this->branch ? ' – ' . $this->branch : '') . ' · FDR #' . $this->fdr_number,
            'amount'        => $this->interest_received,
            'date'          => $date,
            'reference'     => $this->fdr_number,
            'status'        => 'active',
            'source_module' => 'fdr',
            'updated_by'    => $userId,
        ];

        if ($this->linkedIncome) {
            $this->linkedIncome->update($incomeData);
        } else {
            $incomeData['fdr_id']     = $this->id;
            $incomeData['created_by'] = $userId;
            Income::create($incomeData);
        }
    }
}
