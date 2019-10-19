<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Location;
use App\Trend;
use App\Craze;


class CrazeController extends Controller
{

    public function index(Request $request) {

        $country = $request->input('country') ? $request->input('country') : 'United States';

        $date = new \DateTime();
        $date->modify('-62 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');

        $locations = DB::table('locations')
            ->select('locations.name as location')
            ->get();

        $trends = DB::table('crazes')
            ->join('trends', 'crazes.trend', '=', 'trends.id')
            ->join('locations', 'crazes.location', '=', 'locations.id')
            ->select('locations.name as location', 'locations.country_code', 'trends.name as trend', 'trends.url as url', 'crazes.tweet_volume')
            ->where('craze_created_at', '>=', $formatted_date)
            ->where('locations.name', '=', ucfirst($country))
            ->whereNotNull('tweet_volume')
            ->orderByRaw('tweet_volume DESC NULLS LAST')
            ->limit(30)
            ->get();

        // Images from Unsplash
        $unsplash_access_key = env("UNSPLASH_ACCESS_KEY");
        $unsplash_access_secret = env("UNSPLASH_ACCESS_SECRET");

        $client = new \GuzzleHttp\Client();
        $res = $client->get('https://api.unsplash.com/search/photos?per_page=' . count($trends) . '&page=' . rand(1, 5) . '&query=' . $country . '&orientation=portrait&client_id=' . $unsplash_access_key);
        $image_data = json_decode($res->getBody());

        $sorted_by_location = [];

        foreach ($trends as $key => $trend) {
            $sorted_by_location[] = ['name' => $trend->trend, "tweet_volume" => $trend->tweet_volume ?? "Unknown", "url" => $trend->url, "image_url" => $image_data->results[$key]->urls->small ];
        }
        return view('welcome', ['current_location' => $country, 'trends' => $sorted_by_location, 'locations' => $locations]);
    }

    public function get() {

        $settings = array(
            'oauth_access_token' => env("OAUTH_ACCESS_TOKEN"),
            'oauth_access_token_secret' => env("OAUTH_ACCESS_TOKEN_SECRET"),
            'consumer_key' => env("CONSUMER_KEY"),
            'consumer_secret' => env("CONSUMER_SECRET")
        );
        
        $url = 'https://api.twitter.com/1.1/trends/available.json';
        $getfield = '';
        $requestMethod = 'GET';
        
        $twitter = new \TwitterAPIExchange($settings);
        $response = $twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest(true, [CURLOPT_TIMEOUT => 0]);
        $locations = json_decode($response);


        $countries = [];
        foreach ($locations as $location) {
            if($location->placeType->name === "Country") {
                $countries[] = $location;
            }
        }

        foreach ($countries as $country) {
            $location_record = Location::where('woeid', '=', $country->woeid)->first();
            if ($location_record === null) {
                $location_record = new Location;
                $location_record->name = $country->name;
                $location_record->type = $country->placeType->name;
                $location_record->country = $country->country;
                $location_record->woeid = $country->woeid;
                $location_record->country_code = $country->countryCode;
                $location_record->save();
            }
            $url2 = 'https://api.twitter.com/1.1/trends/place.json';
            $getfield2 = '?id=' . $country->woeid;
            $requestMethod2 = 'GET';
            $twitter2 = new \TwitterAPIExchange($settings);
            $trends = $twitter2->setGetfield($getfield2)
                ->buildOauth($url2, $requestMethod2)
                ->performRequest(true, [CURLOPT_TIMEOUT => 0]);
            $trends = json_decode($trends);
            foreach ($trends[0]->trends as $trend) {
                $trend_record = Trend::where('name', '=', $trend->name)->first();
                if ($trend_record === null) {
                    $trend_record = new Trend;
                    $trend_record->name = $trend->name;
                    $trend_record->url = $trend->url;
                    $trend_record->save();
                }
                $craze_record = new Craze;
                $craze_record->trend = $trend_record->id;
                $craze_record->location = $location_record->id;
                $craze_record->craze_created_at = $trends[0]->created_at;
                $craze_record->craze_as_of = $trends[0]->as_of;
                $craze_record->tweet_volume = $trend->tweet_volume;
                $craze_record->promoted_content = $trend->promoted_content;
                $craze_record->save();
            }

        }
        
    }

    public function unsplash(Request $request) {

    }
}
