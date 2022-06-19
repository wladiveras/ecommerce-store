<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FinishRegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    private $register;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($register, $id)
    {
        $register['url'] = route("join-us.finish", $id);
        $this->register = $register;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Só falta mais um passo, {$this->register['reseller']['name']}!")
            ->view('template.mail.finish-register', $this->register);
    }
}
