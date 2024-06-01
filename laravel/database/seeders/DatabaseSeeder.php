<?php

namespace Database\Seeders;

use Carbon\Carbon;
use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->insert([
            'city_name' => 'Zelenograd',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('cities')->insert([
            'city_name' => 'Moscow',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('cities')->insert([
            'city_name' => 'Sochi',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('telegraph_bots')->insert([
            'token' => '6903896292:AAFwThz4Eq0-8CivFqsKf9dOpTozPUkFNZE',
            'name' => 'Phether',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('weatherStatus')->insert([
            'city' => 'Zelenograd',
            'temp' => '38.5',
            'time' => '12:00',
            'mm' => '0.5',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('weatherStatus')->insert([
            'city' => 'Sochi',
            'temp' => '-1.5',
            'time' => '12:00',
            'mm' => '12.5',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Telegraph::registerWebhook()->send();
    }
}
