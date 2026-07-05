<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('mandats:rappels')->daily();

        $schedule->command('rapports:generer --periode=mensuel')->monthlyOn(1, '02:00');
        $schedule->command('rapports:generer --periode=trimestriel')->quarterlyOn(1, '02:15');
        $schedule->command('rapports:generer --periode=annuel')->yearlyOn(1, 1, '02:30');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
