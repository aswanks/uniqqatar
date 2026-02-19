<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteProfileConfirmation extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $userEmail;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->userEmail = $user->email;


    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->view('view.name');
        return $this->view('emails.delete_profile_confirmation')
        ->subject('Profile Deletion Confirmation')
        ->with([
            // 'userName' => $this->user->firstname, // Adjust if you have a different field for the name
            'userEmail' => $this->user->email,

        ]);


    }
}
