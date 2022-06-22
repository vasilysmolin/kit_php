<?php

namespace App\Listeners;

use App\Events\StateEmailEvent;
use App\Mail\StateEmail;
use Illuminate\Support\Facades\Mail;

class StateEmailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  StateEmailEvent  $event
     * @return void
     */
    public function handle(StateEmailEvent $event)
    {
        Mail::to($event->email)->queue(new StateEmail($event->model));
    }
}
