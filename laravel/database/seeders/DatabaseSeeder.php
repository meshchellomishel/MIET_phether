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
            'cityName' => 'Zelenograd',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('cities')->insert([
            'cityName' => 'Moscow',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('cities')->insert([
            'cityName' => 'Sochi',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('telegraph_bots')->insert([
            'token' => '6903896292:AAFwThz4Eq0-8CivFqsKf9dOpTozPUkFNZE',
            'name' => 'Phether',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Telegraph::registerWebhook()->send();
    }
}
