<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class sendWeatherServiceRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-weather-service-request';

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
        $url = 'http://' .env('WEATHER_SERVICE_ADDR', 'localhost').
            ':'.env('WEATHER_SERVICE_PORT',8080).'/api/v1/weather/town';
        info($url);

        $users = DB::table('user__settings')
            ->select('city_id')
            ->groupBy('city_id')
            ->get();

        foreach ($users as &$user) {
            $response = Http::get($url . '/' . $user->city_id);
            info($response);

            if ($response->failed()) {
                error("Failed to get weather for ".$user->city_id." from weather service");
                continue;
            }

            $now = Carbon::now();
            $response = $response->json()["data"];
            DB::table('weatherStatus')
                ->insert([
                    "city_id" => $user->city_id,
                    "temp" => $response["temperature"],
                    "time" => $now,
                    "mm" => $response["mm"],

                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
        }
    }
}
