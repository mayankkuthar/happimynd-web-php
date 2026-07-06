<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HappiBuddyChatMail extends Mailable
{
    use Queueable, SerializesModels;

    public $psychologist;
    public $userName;

    public function __construct($psychologist, $userName)
    {
        $this->psychologist = $psychologist;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('New HappiBUDDY Chat Request')
                    ->from(env('MAIL_FROM_ADDRESS'))
                    ->view('emails.happi_buddy_chat');
    }
}
