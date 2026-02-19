<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShareMemberDetailsMail extends Mailable
{
    use Queueable, SerializesModels;
    public $sharemember;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sharemember)
    {
        $this->sharemember = $sharemember;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $member=$this->sharemember;
        return $this->markdown('emails.shareMemberDetailsSendMail',compact('member'));
    }
}
