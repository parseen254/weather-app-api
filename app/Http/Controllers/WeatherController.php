<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
        ]);

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
    }
}
