<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Mail;

class MailTest extends Command
{
    protected $signature = 'mail:test {--to= : Recipient email address}';
    protected $description = 'Send a test email to verify mail configuration';

    public function handle(): int
    {
        $to = $this->option('to');

        if (!$to) {
            $to = $this->ask('Recipient email address');
        }

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid email address: {$to}");
            return self::FAILURE;
        }

        $this->info("Sending test email to: {$to}");
        $this->info("Mail driver: " . config('mail.default'));
        $this->info("Mail host: " . config('mail.mailers.' . config('mail.default') . '.host', 'n/a'));
        $this->info("From: " . config('mail.from.address') . " <" . config('mail.from.name') . ">");

        $mailable = new class extends Mailable {
            public function envelope(): Envelope
            {
                return new Envelope(subject: 'Unity Circle – Mail Configuration Test');
            }
            public function content(): Content
            {
                return new Content(htmlString: $this->buildHtml());
            }
            private function buildHtml(): string
            {
                $time = now()->format('d M Y H:i:s');
                $driver = config('mail.default');
                $host = config('mail.mailers.' . $driver . '.host', 'n/a');
                $from = config('mail.from.address');
                return "
                <div style='font-family:sans-serif;max-width:560px;margin:32px auto;padding:32px;background:#f0f9ff;border-radius:12px;border:1px solid #bae6fd;'>
                    <h2 style='color:#0369a1;margin:0 0 16px;'>Unity Circle – Mail Test</h2>
                    <p style='color:#334155;margin:0 0 20px;'>This is a test email confirming your mail configuration is working correctly.</p>
                    <table style='width:100%;border-collapse:collapse;'>
                        <tr><td style='padding:6px 0;color:#64748b;font-size:13px;'>Sent at</td><td style='padding:6px 0;color:#0f172a;font-size:13px;font-weight:600;'>{$time}</td></tr>
                        <tr><td style='padding:6px 0;color:#64748b;font-size:13px;'>Driver</td><td style='padding:6px 0;color:#0f172a;font-size:13px;font-weight:600;'>{$driver}</td></tr>
                        <tr><td style='padding:6px 0;color:#64748b;font-size:13px;'>Host</td><td style='padding:6px 0;color:#0f172a;font-size:13px;font-weight:600;'>{$host}</td></tr>
                        <tr><td style='padding:6px 0;color:#64748b;font-size:13px;'>From</td><td style='padding:6px 0;color:#0f172a;font-size:13px;font-weight:600;'>{$from}</td></tr>
                    </table>
                    <p style='margin:20px 0 0;color:#059669;font-weight:600;font-size:14px;'>✓ Email delivery is working!</p>
                </div>";
            }
        };

        try {
            Mail::to($to)->send($mailable);
            $this->info('');
            $this->info('✓ Email sent successfully!');
            if (config('mail.default') === 'log') {
                $this->warn('  Note: MAIL_MAILER=log — email was written to storage/logs/laravel.log, not actually delivered.');
                $this->warn('  Update your .env with real SMTP credentials to send real emails.');
            }
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('');
            $this->error('✗ Email failed: ' . $e->getMessage());
            $this->error('');
            $this->line('Troubleshooting:');
            $this->line('  1. Verify MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD in .env');
            $this->line('  2. For Hostinger: MAIL_HOST=mail.yourdomain.com, MAIL_PORT=465, MAIL_ENCRYPTION=ssl');
            $this->line('  3. Run: php artisan config:clear  then retry');
            $this->line('  4. Check storage/logs/laravel.log for the full error');
            return self::FAILURE;
        }
    }
}
