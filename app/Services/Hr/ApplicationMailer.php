<?php

namespace App\Services\Hr;

use Illuminate\Support\Facades\Mail;

/**
 * Sends candidate email through the SMTP account from env (API_SPEC §8).
 *
 * Laravel's mailer is configured at runtime because the HR connection and this
 * mailbox are independent of the app's default MAIL_* settings.
 */
class ApplicationMailer
{
    public function configured(): bool
    {
        $mail = (array) config('hr.mail', []);

        foreach (['host', 'username', 'password', 'from'] as $key) {
            if ((string) ($mail[$key] ?? '') === '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array{0: bool, 1: ?string, 2: ?string} [sent, message_id, error]
     */
    public function send(string $to, string $subject, string $body): array
    {
        $mail = (array) config('hr.mail', []);
        $from = $this->splitFrom((string) ($mail['from'] ?? ''));

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => (string) $mail['host'],
            'mail.mailers.smtp.port' => (int) $mail['port'],
            'mail.mailers.smtp.username' => (string) $mail['username'],
            'mail.mailers.smtp.password' => (string) $mail['password'],
            // Port 587 uses STARTTLS; SMTP_SECURE=1 means implicit TLS (465).
            'mail.mailers.smtp.encryption' => !empty($mail['secure']) ? 'ssl' : 'tls',
            'mail.from.address' => $from[0],
            'mail.from.name' => $from[1],
        ]);

        Mail::forgetMailers();

        try {
            Mail::raw($body, function ($message) use ($to, $subject, $from) {
                $message->to($to)->subject($subject);

                if ($from[0] !== '') {
                    $message->from($from[0], $from[1]);
                }
            });
        } catch (\Throwable $e) {
            return [false, null, $this->cleanError($e->getMessage())];
        }

        return [true, null, null];
    }

    /** `Name <address>` → [address, name]; a bare address keeps an empty name. */
    private function splitFrom(string $value): array
    {
        $value = trim($value);

        if (preg_match('/^\s*(.*?)\s*<\s*([^>]+)\s*>\s*$/', $value, $m)) {
            return [trim($m[2]), trim($m[1], " \t\"'")];
        }

        return [$value, ''];
    }

    /** SMTP errors can be multi-line and mention internals; keep one short line. */
    private function cleanError(string $message): string
    {
        $message = trim((string) preg_replace('/\s+/', ' ', $message));

        return mb_substr($message, 0, 2000);
    }
}
