<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Location extends Model
{

    public function get() {
        $locations =  DB::table('locations')
        ->select('locations.name as location', 'locations.id as location_id')
        ->get()
        ->all();

        return $locations;
    }

    public function get_with_crazes($locationsCacheKey) {
        $locations = \Cache::remember($locationsCacheKey, now()->addHours(2400), function() {
            return DB::table('locations')
            ->join('crazes', 'locations.id', '=', 'crazes.location')
            ->select('locations.name as location', 
                DB::raw('COUNT(DISTINCT crazes.id) as craze_count')
            )
            ->groupBy('locations.name')
            ->orderBy('locations.name')
            ->get()
            ->all();
        });

        return $locations;
    }
}
