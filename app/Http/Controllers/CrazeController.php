<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;
use App\Location;
use App\Trend;
use App\Craze;
use App\LocationImage;


class CrazeController extends Controller
{

    public function __construct() {
        $this->environment =  env("APP_ENV");
    }

    public function index(Request $request) {

        $agent = new Agent();
        $country = $request->input('country') ? $request->input('country') : 'Worldwide';
        $input_end_date = new \DateTime($request->input('date'));
        $input_end_date = $input_end_date->modify('tomorrow');
        $end_date = $input_end_date->format('Y-m-d H:i:s');
        $redis_end_date = $input_end_date->format('Y-m-d H');
        $start_date = $input_end_date->modify('-24 hours');
        $start_date = $start_date->format('Y-m-d H:i:s');
        $this->country  = $country;
        $this->start_date  = $start_date;
        $this->end_date = $end_date;

        $locations = new Location();
        $locations = $locations->get_with_crazes('trends');
        $worldwide = $locations[count($locations) - 1];
        array_pop($locations);
        array_unshift($locations, $worldwide);

        $trends = new Trend();
        $trends = $trends->get_trends($this->start_date, $this->end_date, $this->country);

        $images = new LocationImage();

        if($agent->isMobile()) {
            $images = $images->get_mobile($this->country);
        }
        else {
            $images = $images->get($this->country);
        }

        $trends_present = [];
        $sorted_by_location = [];

        $image_count = count($images);

        foreach ($trends as $key => $trend) {
            if(!in_array($trend->trend, $trends_present)) {
                if($key >= $image_count) {
                    $key = rand(0, $image_count -1);
                }
                $sorted_by_location[] = [
                'name' => $trend->trend,
                "tweet_volume" => $trend->tweet_volume ?? "Unknown",
                "url" => $trend->url,
                "image_url" => $images[$key]->url,
                "image_html_url" => $images[$key]->html_url,
                "image_username" =>  $images[$key]->username
                ];
                $trends_present[] = $trend->trend;
            }
        }

        return view('welcome', [
            'current_location' => $country, 
            'trends' => $sorted_by_location, 
            'locations' => $locations, 
            'image_count' => $image_count,
            'is_mobile' => $agent->isMobile() ? "True" : "False"
            ] );
    }

    public function test() {
        return "<h1>TESTING</h1>";
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
            if($location->placeType->name === "Country" || $location->placeType->name === "Supername") {
                $countries[] = $location;
            }
        }

        foreach ($countries as $country) {
            $location_record = Location::where('woeid', '=', $country->woeid)->first();
            if ($location_record === null) {
                $location_record = new Location;
                $location_record->name = $country->name;
                $location_record->type = isset($country->placeType->name) ? $country->placeType->name : 'Worldwide';
                $location_record->country = $country->country;
                $location_record->woeid = $country->woeid;
                $location_record->country_code = $country->name == 'Worldwide' ? 'WW' : $country->countryCode;
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

    public function save_images(Request $request) {

        $locations = new Location();
        $locations = $location->get();
        
        foreach ($locations as $key => $country) {

            $location_images = new LocationImage();
        
            $existing_country_images = $location_images->get_by_id($country->location_id);

            if(count($existing_country_images) < 100) { 

                $unsplash_access_key = env("UNSPLASH_ACCESS_KEY");
                $unsplash_access_secret = env("UNSPLASH_ACCESS_SECRET");

                $client = new \GuzzleHttp\Client();
                $res = $client->get('https://api.unsplash.com/search/photos?per_page=30&page=' . rand(1, 5) . '&query=' . (trim($country->location) == 'Worldwide' ? 'landscape' : $country->location)  . '&orientation=portrait&client_id=' . $unsplash_access_key);
                $image_data = json_decode($res->getBody());

                $current_location_id = DB::table('locations')
                    ->select('id as location_id')
                    ->where('name', '=', $country->location)
                    ->pluck('location_id')
                    ->first();
                
                foreach ($image_data->results as $key => $image) {

                    $existing_image_record = DB::table('location_images')
                        ->where('location_images.unsplash_id', '=', $image->id)
                        ->get()
                        ->all();

                    if(count($existing_image_record) > 0) {
                        continue;
                    }

                    $location_image_record = new LocationImage;
                    $location_image_record->location = $current_location_id;
                    $location_image_record->unsplash_id = $image->id;
                    $location_image_record->alt_description = $image->alt_description || "Image of " . $country->location;
                    $location_image_record->url = $image->urls->small;
                    $location_image_record->html_url = $image->links->html;
                    $location_image_record->username = $image->user->username;
                    $location_image_record->save();
                }

                echo("New images saved");
            }

        }
    }
}
