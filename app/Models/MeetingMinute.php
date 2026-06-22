<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingMinute extends Model
{
    protected $fillable = [
        'title', 'meeting_date', 'content', 'attachment', 'is_public', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
            'is_public' => 'boolean',
        ];
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
