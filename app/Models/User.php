<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'photo', 'address',
        'date_of_birth', 'profession', 'emergency_contact',
        'nominee_name', 'nominee_contact', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['photo_url'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function membershipApplication()
    {
        return $this->hasOne(MembershipApplication::class);
    }

    public function feeSubmissions()
    {
        return $this->hasMany(MonthlyFeeSubmission::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isTreasurer(): bool
    {
        return $this->hasRole('treasurer');
    }

    public function isMember(): bool
    {
        return $this->hasRole('member');
    }

    public function isAdminOrTreasurer(): bool
    {
        return $this->hasAnyRole(['admin', 'treasurer']);
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? '') . '&color=7F9CF5&background=EBF4FF';
    }
}
