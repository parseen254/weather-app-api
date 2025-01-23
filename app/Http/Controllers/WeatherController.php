<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
        ]);

        $cacheKey = "weather_{$request->lat}_{$request->lon}";
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($request) {
            $response = Http::get('https://api.openweathermap.org/data/3.0/onecall', [
                'lat' => $request->lat,
                'lon' => $request->lon,
                'exclude' => 'minutely,hourly',
                'appid' => env('OPENWEATHER_API_KEY'),
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Failed to fetch weather data'
                ], 500);
            }

            return $response->json();
        });
    }
}
