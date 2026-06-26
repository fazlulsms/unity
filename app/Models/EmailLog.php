<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $fillable = [
        'to_email', 'to_name', 'subject', 'mailable_class',
        'loggable_type', 'loggable_id',
        'status', 'error_message', 'sent_by',
    ];

    public function loggable()
    {
        return $this->morphTo();
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function getMailableShortNameAttribute(): string
    {
        return class_basename($this->mailable_class ?? '');
    }
}
