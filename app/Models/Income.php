<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'income';

    protected $fillable = [
        'date', 'income_type', 'source', 'amount', 'reference',
        'attachment', 'notes', 'status', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
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

    public function getIncomeTypeLabelAttribute(): string
    {
        return match ($this->income_type) {
            'fdr_interest'       => 'FDR Interest',
            'donation'           => 'Donation',
            'extra_contribution' => 'Extra Contribution',
            default              => 'Other',
        };
    }
}
