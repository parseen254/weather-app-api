<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\WeatherController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Weather API Documentation"
 * )
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 */

Route::prefix('cities')->group(function () {
    Route::get('/search', [CityController::class, 'search']);
});

Route::get('/weather', [WeatherController::class, 'getWeather']);
