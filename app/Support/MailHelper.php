<?php

namespace App\Support;

use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class MailHelper
{
    public static function send(
        string $toEmail,
        string $toName,
        Mailable $mailable,
        ?Model $loggable = null,
        ?int $sentBy = null
    ): bool {
        $subject = '';
        try {
            $subject = $mailable->envelope()->subject;
        } catch (\Throwable) {}

        try {
            Mail::to($toEmail, $toName)->send($mailable);

            EmailLog::create([
                'to_email'       => $toEmail,
                'to_name'        => $toName,
                'subject'        => $subject,
                'mailable_class' => get_class($mailable),
                'loggable_type'  => $loggable ? get_class($loggable) : null,
                'loggable_id'    => $loggable?->id,
                'status'         => 'sent',
                'sent_by'        => $sentBy,
            ]);

            return true;
        } catch (\Exception $e) {
            logger()->error("Email failed [{$subject}] to {$toEmail}: " . $e->getMessage());

            EmailLog::create([
                'to_email'       => $toEmail,
                'to_name'        => $toName,
                'subject'        => $subject,
                'mailable_class' => get_class($mailable),
                'loggable_type'  => $loggable ? get_class($loggable) : null,
                'loggable_id'    => $loggable?->id,
                'status'         => 'failed',
                'error_message'  => $e->getMessage(),
                'sent_by'        => $sentBy,
            ]);

            return false;
        }
    }

    /**
     * Send to all admin and treasurer users.
     * Accepts a factory callable so a fresh mailable is used per recipient.
     */
    public static function sendToAdmins(
        callable $mailableFactory,
        ?Model $loggable = null,
        ?int $sentBy = null
    ): void {
        $admins = User::role(['admin', 'treasurer'])
            ->whereNotNull('email')
            ->where('email', 'not like', '%@unity.local')
            ->get();

        foreach ($admins as $admin) {
            static::send($admin->email, $admin->name, $mailableFactory(), $loggable, $sentBy);
        }
    }

    public static function validEmail(?string $email): bool
    {
        return $email && !str_ends_with($email, '@unity.local') && filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
