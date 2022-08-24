<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected $hash;

    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('welcome@tapigo.ru', "Подтверждение email")
            ->markdown('emails.password.verify')
            ->subject('Подтверждение email')
            ->with([
                'hash' => $this->hash,
            ]);
    }
}
