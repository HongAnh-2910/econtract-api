<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationSendUser extends Mailable
{
    use Queueable, SerializesModels;

    protected string $type;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email =  env('MAIL_FROM_ADDRESS');
        return $this->from($email, 'Test')
            ->view('emails.applications.send-mail-user')
            ->with([
                'type' => $this->type
            ])
            ;
    }
}
