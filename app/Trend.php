<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Trend extends Model
{
    public function get_trends($start_date, $end_date, $country) {

        return DB::table('crazes')
        ->join('trends', 'crazes.trend', '=', 'trends.id')
        ->join('locations', 'crazes.location', '=', 'locations.id')
        ->select('locations.name as location', 'locations.country_code', 'trends.name as trend', 'trends.url as url', 'crazes.tweet_volume')
        ->where('crazes.created_at', '>=', $start_date)
        ->where('crazes.created_at', '<', $end_date)
        ->where('locations.name', '=', ucfirst($country))
        ->orderByRaw('tweet_volume DESC NULLS LAST')
        ->distinct()
        ->get()
        ->all();

    }
}
