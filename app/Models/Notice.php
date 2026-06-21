<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title', 'content', 'type', 'is_public', 'published_at', 'published_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_public' => 'boolean',
        ];
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
