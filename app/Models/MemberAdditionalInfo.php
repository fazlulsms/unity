<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberAdditionalInfo extends Model
{
    protected $fillable = [
        'member_id',
        'present_address',
        'permanent_address',
        'business_address',
        'primary_emergency_name',
        'primary_emergency_relationship',
        'primary_emergency_phone',
        'secondary_emergency_name',
        'secondary_emergency_relationship',
        'secondary_emergency_phone',
        'marital_status',
        'religion',
        'marriage_anniversary',
        'blood_group',
        'nationality',
        'nid_passport',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'marriage_anniversary' => 'date',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
