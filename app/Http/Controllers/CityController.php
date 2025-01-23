<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $perPage = $request->input('per_page', 10);
        
        // Find matching cities by name or country with pagination
        $cities = City::where('name', 'LIKE', "%$query%")
            ->orWhere('country', 'LIKE', "%$query%")
            ->paginate($perPage);

        // Transform the paginated results
        $cities->through(function ($city) {
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
                ->whereRaw('(6371 * acos(cos(radians(?)) 
                     * cos(radians(lat)) 
                     * cos(radians(lon) - radians(?)) 
                     + sin(radians(?)) 
                     * sin(radians(lat)))) >= 20', 
                    [$city->lat, $city->lon, $city->lat])
                ->orderBy('distance')
                ->take(4)
                ->get();

            return [
                'city' => $city,
                'neighbors' => $neighbors
            ];
        });

        return $cities;
    }
}
