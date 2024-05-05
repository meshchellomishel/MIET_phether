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
        Schema::create('user__settings', function (Blueprint $table) {
            $table->BigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('city_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->string('cityName');
            $table->time('notifyTime');
            $table->boolean('changeNotify');
            $table->boolean('mute');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user__settings');
    }
};
