<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankWithdrawal extends Model
{
    protected $fillable = [
        'bank_account_id', 'withdrawal_date', 'amount',
        'cheque_number', 'purpose', 'remarks', 'attachment',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'withdrawal_date' => 'date',
            'amount'          => 'decimal:2',
        ];
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment ? url('uploads/' . $this->attachment) : null;
    }
}
