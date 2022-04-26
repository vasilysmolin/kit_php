<?php

namespace App\Mail;

use App\Objects\Reasons\Reasons;
use App\Objects\States\States;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StateEmail extends Mailable implements ShouldQueue
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
            ->from('welcome@tapigo.ru', "Ваше объявление \"{$this->model->name}\"")
            ->markdown('emails.states.state')
            ->subject('Смена статуса объявления')
            ->with([
                'model' => $this->model,
//                'states' => new States(),
//                'reasons' => new Reasons(),
            ]);
    }
}
