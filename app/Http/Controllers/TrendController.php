<?php

namespace App\Http\Controllers;

class TrendController extends Controller
{

    public function index() {

        // $settings = array(
        //     'oauth_access_token' => env("OAUTH_ACCESS_TOKEN"),
        //     'oauth_access_token_secret' => env("OAUTH_ACCESS_TOKEN_SECRET"),
        //     'consumer_key' => env("CONSUMER_KEY"),
        //     'consumer_secret' => env("CONSUMER_SECRET")
        // );
        
        // $url = 'https://api.twitter.com/1.1/trends/available.json';
        // $getfield = '';
        // $requestMethod = 'GET';
        
        // $twitter = new \TwitterAPIExchange($settings);
        // $response = $twitter->setGetfield($getfield)
        //     ->buildOauth($url, $requestMethod)
        //     ->performRequest(true, [CURLOPT_TIMEOUT => 0]);
        // $locations = json_decode($response);
        // $locations_file = fopen('/Users/magnusvaughan/side/hotbackthen/resources/json/locations.json', 'w');
        // fwrite($locations_file, $response);
        // fclose($locations_file);

        // $countries = [];
        // foreach ($locations as $location) {
        //     if($location->placeType->name === "Country") {
        //         $countries[] = $location;
        //     }
        // }
        
        // foreach ($countries as $country) {
        //     $url2 = 'https://api.twitter.com/1.1/trends/place.json';
        //     $getfield2 = '?id=' . $country->woeid;
        //     $requestMethod2 = 'GET';
        //     $twitter2 = new \TwitterAPIExchange($settings);
        //     $trend = $twitter2->setGetfield($getfield2)
        //         ->buildOauth($url2, $requestMethod2)
        //         ->performRequest(true, [CURLOPT_TIMEOUT => 0]);

        //     $trendjson = fopen('/Users/magnusvaughan/side/hotbackthen/resources/json/' . $country->countryCode . '.json', 'w');
        //     fwrite($trendjson, $trend);
        //     fclose($trendjson);

        // }

        $country_json_file = json_decode(file_get_contents(base_path() . "/resources/json/US.json"));
        foreach ($country_json_file[0]->trends as $trend) {
            var_dump($trend);
        }
        
    }

}
