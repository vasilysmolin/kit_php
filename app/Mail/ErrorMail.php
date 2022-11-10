<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ErrorMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $errors;
    protected $template;

    public function __construct(Collection $errors, string $template = 'error')
    {
        $this->errors = $errors;
        $this->template = $template;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = $this->template;
        return $this
            ->from('welcome@tapigo.ru', "Ошибка на тапиго {$this->errors['url']}")
            ->markdown("emails.errors.$template")
            ->subject('500 ошибка')
            ->with([
                'errors' => $this->errors,
            ]);
    }
}
