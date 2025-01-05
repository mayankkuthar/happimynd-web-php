<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailDetails;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailDetails)
    {
        //
        $this->mailDetails = $mailDetails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->Subject('Generated Code')
                    ->markdown('mail.otp');
    }
}
