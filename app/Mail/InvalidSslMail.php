<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\SslCertificate\SslCertificate;

class InvalidSslMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $dataSslCertificate;

    public function __construct(SslCertificate $dataSslCertificate)
    {
        $this->dataSslCertificate = $dataSslCertificate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('welcome@tapigo.ru', __('email.title.sslExpired') . "," . "осталось {$this->dataSslCertificate->daysUntilExpirationDate()} дней")
            ->markdown('emails.ssl.invalid')
            ->subject(__('email.title.sslExpired'))
            ->with([
                'daysUntilExpirationDate' => $this->dataSslCertificate->daysUntilExpirationDate(),
            ]);
    }
}
