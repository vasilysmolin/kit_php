<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyStepsUserEmail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('welcome@tapigo.ru', "Вы успешно зарегистрировались, осталось совсем чуть-чуть")
            ->markdown('emails.boarding.steps')
            ->subject('Вы успешно зарегистрировались, осталось совсем чуть-чуть')
            ->with([
                'model' => $this->model,
            ]);
    }
}
