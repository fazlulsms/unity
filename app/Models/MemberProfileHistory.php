<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberProfileHistory extends Model
{
    protected $fillable = ['member_id', 'changes', 'updated_by'];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
