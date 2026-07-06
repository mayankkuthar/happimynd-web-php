<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $psychologist;
    public $userName;
    public $date;
    public $time;

    public function __construct($psychologist, $userName, $date, $time)
    {
        $this->psychologist = $psychologist;
        $this->userName = $userName;
        $this->date = $date;
        $this->time = $time;
    }

    public function build()
    {
        return $this->subject('New HappiTALK Session Booking')
                    ->from(env('MAIL_FROM_ADDRESS'))
                    ->view('emails.new_booking');
    }
}
