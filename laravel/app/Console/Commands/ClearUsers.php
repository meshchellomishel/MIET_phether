<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $reset_time = Carbon::parse("00:00");
        if ($now->diffInMinutes($reset_time) > 1 ||
            $now->lt($reset_time))
            return;

        DB::table('user__settings')
            ->where('notified', '=', 'true')
            ->update(['notified' => 'false']);

        DB::table('weatherStatus')
            ->where('weather_changed', '=', 'true')
            ->update(['weather_changed' => 'false']);
    }
}
