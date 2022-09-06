<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmailLink extends Mailable
{
    use Queueable, SerializesModels;


    public $name,$email,$role;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$email,$role)
    {
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.user-invitations')->with([
            'name'=>$this->name,
            'email'=>$this->email,
            'role'=>$this->role
        ]);
        
    }
}
