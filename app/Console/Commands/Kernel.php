<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\UpdatePartnerLevels::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('partners:update-levels')->monthly();
        $schedule->command('sponsorships:update-statuses')->daily();

    }


}
