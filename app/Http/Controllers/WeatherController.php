<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Weather API Documentation",
 *     description="API documentation for the Weather Application"
 * )
 */
class WeatherController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/weather",
     *     summary="Get weather information",
     *     tags={"Weather"},
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         description="Latitude",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="lon",
     *         in="query",
     *         description="Longitude",
     *         required=true,
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Weather data retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to fetch weather data"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
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
