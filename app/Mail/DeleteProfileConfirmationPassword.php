<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteProfileConfirmationPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $password;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Confirm Account Deletion')
        ->view('emails.delete_prifile_confirmation_password')
        ->with([
            'userName' => $this->user->name,
            'password' => $this->password,
        ]);

        // return $this->view('view.name');
    }
}
