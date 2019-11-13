<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LocationImage extends Model
{

    public function get($country) {
        return DB::table('location_images')
            ->join('locations', 'location_images.location', '=', 'locations.id')
            ->where('locations.name', 'LIKE', '%'.$country.'%')
            ->inRandomOrder()
            ->get()
            ->all();
    }

    public function get_mobile($country) {
       return DB::table('location_images')
            ->join('locations', 'location_images.location', '=', 'locations.id')
            ->where('locations.name', 'LIKE', '%'.$country.'%')
            ->limit(10)
            ->inRandomOrder()
            ->get()
            ->all();
    }

    public function get_by_id($country_id) {
        DB::table('location_images')
        ->join('locations', 'location_images.location', '=', 'locations.id')
        ->select('location_images.id as location_images_id','locations.id as locations_id')
        ->where('location_images.location', '=', strval($country_id))
        ->get()
        ->all();
    }
}
