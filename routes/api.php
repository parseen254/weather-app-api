<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\WeatherController;

Route::prefix('cities')->group(function () {
    Route::get('/search', [CityController::class, 'search']);
});

Route::get('/weather', [WeatherController::class, 'getWeather']);
