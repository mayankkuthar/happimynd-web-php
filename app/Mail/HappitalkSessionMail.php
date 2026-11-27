<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * HappiTALK booking intimation / session reminder email (user or psychologist).
 * Takes plain values so the queued job doesn't depend on models.
 */
class HappitalkSessionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $name;
    public $bodyText;
    public $details;

    public function __construct(string $subjectLine, string $name, string $bodyText, array $details)
    {
        $this->subjectLine = $subjectLine;
        $this->name = $name;
        $this->bodyText = $bodyText;
        $this->details = $details;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->from(env('MAIL_FROM_ADDRESS'))
            ->markdown('emails.happitalk_session');
    }
}
