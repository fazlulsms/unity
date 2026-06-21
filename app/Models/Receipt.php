<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'receipt_number', 'monthly_fee_submission_id', 'member_id',
        'member_name', 'month', 'year', 'amount', 'payment_method',
        'payment_date', 'approved_date', 'authorized_by', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'approved_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function submission()
    {
        return $this->belongsTo(MonthlyFeeSubmission::class, 'monthly_fee_submission_id');
    }

    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public static function generateReceiptNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->count() + 1;
        return 'RCP-' . $year . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
