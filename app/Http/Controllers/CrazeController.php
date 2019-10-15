<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Location;
use App\Trend;
use App\Craze;


class CrazeController extends Controller
{

    public function index() {
        return 'This is the front';
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
}
