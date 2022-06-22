<?php

namespace App\Providers;

use App\Events\SaveLogsEvent;
use App\Events\StateEmailEvent;
use App\Listeners\SaveLogsListener;
use App\Listeners\StateEmailListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        StateEmailEvent::class => [
            StateEmailListener::class,
        ],
        SaveLogsEvent::class => [
            SaveLogsListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
    }
}
