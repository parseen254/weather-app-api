<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;

Route::prefix('cities')->group(function () {
    Route::get('/search', [CityController::class, 'search']);
});
