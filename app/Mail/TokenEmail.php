<?php

namespace App\Mail;

use App\Models\DataGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TokenEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailDetails, $content;
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
            ->subject($this->mailDetails['subject'])
            ->attach($this->mailDetails['path'], [
                'as' => 'HappiMynd Process Note',
                'mime' => 'application/pdf'
            ])
            ->markdown('emails.token_email');
    }
}
