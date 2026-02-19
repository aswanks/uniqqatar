<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventConfirmationReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $register;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($register)
    {
        $this->register = $register;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Event Registration Confirmation')
                            ->markdown('emails.EventConfirmationReplyMail', [
                                'register' => $this->register
                            ]);
    }
}