<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'original_id',
        'name',
        'state',
        'country',
        'lat',
        'lon'
    ];

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', '%'.$term.'%');
    }

    public function scopeInCountry($query, $countryCode)
    {
        return $query->where('country', strtoupper($countryCode));
    }

    public function scopeNearby($query, $lat, $lon, $radiusKm = 50)
    {
        return $query->whereRaw(
            '(6371 * acos(cos(radians(?)) * 
             cos(radians(lat)) * 
             cos(radians(lon) - radians(?)) + 
             sin(radians(?)) * 
             sin(radians(lat)))) <= ?',
            [$lat, $lon, $lat, $radiusKm]
        );
    }
}
