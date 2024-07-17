<?php

namespace App\Console;

use App\Http\Controllers\PremiumController;
use App\Models\PremiumUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('delete-premium-users')->everyMinute(); to fix later :)
        $schedule->call(function (){
            PremiumUsers::query()->where('end_date', '<', now());
        })->daily();
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
