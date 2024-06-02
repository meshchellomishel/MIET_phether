<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weatherStatus', function (Blueprint $table) {
            $table->BigIncrements('id');
            $table->bigInteger('city_id');
            $table->float('temp');
            $table->time('time');
            $table->float('mm');
            $table->boolean('weather_changed')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weatherStatus');
    }
};
