<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $registration;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($registration, $password)
    {
        $this->registration = $registration;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $registration=$this->registration;
        $password=$this->password;
        
        return $this->markdown('emails.RegistrationSendMail',compact('registration','password'));
    }
}
