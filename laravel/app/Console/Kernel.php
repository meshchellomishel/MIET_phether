<?php

namespace App\Console;

use Ensi\LaravelInitialEventPropagation\SetInitialEventArtisanMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    public function bootstrap(): void
    {
        parent::bootstrap();
        (new SetInitialEventArtisanMiddleware())->handle();
    }

    /**
     * Define the application's command schedule.
     *
     */
    protected function schedule(Schedule $schedule): void
    {
//        $schedule->call(
//            function() {
//
//            }
//        )->everyMinute();

//        $schedule->call(function () {
//            $users = DB::table('user__settings')
//                ->where('mute', '=', false)
//                ->whereRaw('ABS(TIMESTAMPDIFF(SECOND, datetime, ?)) <= 60', [Carbon::now()]);
//        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
