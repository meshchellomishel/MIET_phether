<?php

use App\Http\Web\Controllers\HealthCheck;
use App\Http\Web\Controllers\OasController;
use App\Http\Web\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('health', HealthCheck::class);
Route::post('weather', [WeatherController::class, 'store']);
Route::get('/', [OasController::class
                 , 'list']);
