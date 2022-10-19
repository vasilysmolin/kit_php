<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('user-parse')->everyFourHours();
        $schedule->command('user-boarding')->daily()->at('09:00');
        $schedule->command('sorting')->daily()->at('05:00');
        $schedule->command('scout:import')->daily()->at('06:00');

        $schedule->command('realty-import')->daily()->at('05:30');

        if (config('app.env') === 'production') {
            // Backups pgsql
            $schedule->command('backup:run', ['--only-db'])->everyFourHours();
            $schedule->command('backup:clean')->daily()->at('08:00');
            $schedule->command('backup:monitor')->daily()->at('10:00');

            $schedule->command('check-ssl')->daily()->at('12:00');
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
