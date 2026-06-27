<?php

namespace App\Models;

use App\Traits\ResolvesUploadedPhoto;
use Illuminate\Database\Eloquent\Model;

class MemberFamilyMember extends Model
{
    use ResolvesUploadedPhoto;

    protected $fillable = [
        'member_id',
        'type',
        'relationship',
        'name',
        'sex',
        'date_of_birth',
        'profession',
        'organization',
        'phone',
        'email',
        'photo',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return static::resolvedPhotoUrl($this->photo);
    }

    public function getRelationshipLabelAttribute(): string
    {
        if ($this->type === 'other') {
            return $this->relationship ?: 'Other';
        }
        return ucfirst($this->type);
    }
}
