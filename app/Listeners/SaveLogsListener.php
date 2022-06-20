<?php

namespace App\Listeners;

use App\Events\SaveLogsEvent;
use App\Models\SearchLogs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class SaveLogsListener implements ShouldQueue
{
    use Queueable;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SaveLogsEvent $event)
    {
        if (Str::length($event->query) >= 4) {
            $log = new SearchLogs();
            $log->text = $event->query;
            $log->user_id = isset($event->user) ? $event->user->getKey() : null;
            $log->type = $event->type;
            $log->save();
        }
    }
}
