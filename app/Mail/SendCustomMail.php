<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCustomMail extends Mailable
{
    use Queueable, SerializesModels;
    public $sendcustomemail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sendcustomemail)
    {
        $this->sendcustomemail = $sendcustomemail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        $sendcustommessage=$this->sendcustomemail;
        return $this->markdown('emails.sendCustomSendMail',compact('sendcustommessage'));
    }
}
