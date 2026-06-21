<?php

namespace App\Mail;

use App\Models\Member;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMember extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Member $member,
        public string $password,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to Unity Club – Your Membership is Approved!');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.welcome-member');
    }
}
