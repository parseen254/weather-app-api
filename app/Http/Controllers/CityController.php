<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cities/search",
     *     tags={"Cities"},
     *     summary="Search cities",
     *     description="Search cities by name or country and get nearby cities",
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search term for city name or country",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of results per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of cities with their neighbors",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="city", type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="country", type="string"),
     *                         @OA\Property(property="lat", type="number"),
     *                         @OA\Property(property="lon", type="number")
     *                     ),
     *                     @OA\Property(property="neighbors", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string"),
     *                             @OA\Property(property="country", type="string"),
     *                             @OA\Property(property="lat", type="number"),
     *                             @OA\Property(property="lon", type="number"),
     *                             @OA\Property(property="distance", type="number")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     )
     * )
     */
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
