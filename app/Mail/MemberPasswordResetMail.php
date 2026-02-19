<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;
    public $member;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($member, $password)
    {
        $this->member = $member;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $member=$this->member;
        $password=$this->password;
        
        return $this->markdown('emails.MemberPasswordReset',compact('member','password'));

    }
}