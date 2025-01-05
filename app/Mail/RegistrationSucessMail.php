<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationSucessMail extends Mailable
{
    use Queueable, SerializesModels;
public $psychologist;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($psychologist)
    {
        $this->psychologist=$psychologist;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mail from Happimynd')->from(env('MAIL_FROM_ADDRESS'))->view('Backend.psychologist.account_register');
    }
}
