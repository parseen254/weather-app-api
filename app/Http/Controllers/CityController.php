<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        // Find matching cities by name or country
        $cities = City::where('name', 'LIKE', "%$query%")
            ->orWhere('country', 'LIKE', "%$query%")
            ->get();

        // For each city, find 3 nearest neighbors
        return $cities->map(function ($city) {
            $neighbors = City::select('*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) 
                     * cos(radians(lat)) 
                     * cos(radians(lon) - radians(?)) 
                     + sin(radians(?)) 
                     * sin(radians(lat)))) AS distance',
                    [$city->lat, $city->lon, $city->lat]
                )
                ->where('id', '!=', $city->id)
                ->orderBy('distance')
                ->take(3)
                ->get();

            return [
                'city' => $city,
                'neighbors' => $neighbors
            ];
        });
    }
}
