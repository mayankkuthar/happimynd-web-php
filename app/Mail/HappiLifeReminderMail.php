<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HappiLifeReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $progress;
    public $reminderMessage;

    public function __construct($user, array $progress, string $reminderMessage)
    {
        $this->user = $user;
        $this->progress = $progress;
        $this->reminderMessage = $reminderMessage;
    }

    public function build()
    {
        $subject = $this->progress['answered'] > 0
            ? "You're {$this->progress['percent']}% through HappiLIFE — finish your Self Check In"
            : 'Your HappiLIFE Self Check In is waiting';

        return $this->subject($subject)
            ->from(env('MAIL_FROM_ADDRESS'))
            ->markdown('emails.happilife_reminder', [
                'name' => $this->user->name ?: ($this->user->nickname ?: $this->user->username),
                'progress' => $this->progress,
                'reminderMessage' => $this->reminderMessage,
                'resumeUrl' => config('happilife.resume_url'),
            ]);
    }
}
