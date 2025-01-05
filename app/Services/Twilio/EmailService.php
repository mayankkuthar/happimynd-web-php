<?php

namespace App\Services\Twilio;

class EmailService
{
    public function __construct($sendgrid_key = config('twilio.sendgrid_api_key'))
    {
        $this->email = new SendGrid\Mail\Mail();
    }
}
