<?php

namespace App\Console;

use App\Console\Commands\SyncProductsFromAPI;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sync:products')->dailyAt('00:00');
    }

    protected $commands = [
        SyncProductsFromAPI::class,
    ];
}
