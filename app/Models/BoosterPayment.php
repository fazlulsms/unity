<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoosterPayment extends Model
{
    protected $fillable = [
        'booster_contribution_id', 'member_id', 'payment_date', 'amount',
        'payment_method', 'reference', 'attachment', 'remarks',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount'       => 'decimal:2',
        ];
    }

    public function boosterContribution()
    {
        return $this->belongsTo(BoosterContribution::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
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
