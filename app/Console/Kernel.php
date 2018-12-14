<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Standard Laravel Console Kernel
 */
class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule A schedule instance.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(null);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        // phpcs:disable PEAR
        require base_path('routes/console.php');
    }
}
