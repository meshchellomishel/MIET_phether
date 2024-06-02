<?php

namespace App;

use Carbon\CarbonInterface;
use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;


/**
 * @property int $usesUniqueIds
 * @property string $cityName
 *
 * @property CarbonInterface|null $created_at
 * @property CarbonInterface|null $updated_at
 */
class City extends Model
{
    public static function parse_city($city_data)
    {
        $city_data = explode(',', $city_data);
        if (count($city_data) > 3)
            return null;

        $city_name = "";
        $city_state = "";
        $city_country = "";
        if (count($city_data) == 1) {
            $city_name = $city_data[0];
        } else if (count($city_data) == 2) {
            $city_state = $city_data[0];
            $city_name = $city_data[1];
        } else if (count($city_data) == 3) {
            $city_country = $city_data[0];
            $city_state = $city_data[1];
            $city_name = $city_data[2];
        }

        return [
            "name" => $city_name,
            "state" => $city_state,
            "country" => $city_country
        ];
    }

    public static function get_from_API($city_data, $api_key)
    {
        return Http::withHeaders([
            'X-Api-Key' => $api_key
        ])->get('https://api.api-ninjas.com/v1/geocoding',[
            'city' => $city_data['name'],
            'state' => $city_data['state'],
            'country' => $city_data['country'],
        ])->json();
    }

    public static function push_to_weather_service($city_data)
    {
        $url = 'http://' .env('WEATHER_SERVICE_ADDR', 'localhost').
            ':'.env('WEATHER_SERVICE_PORT',8080).'/api/v1/towns/towns';
        info($url);
        $response = Http::post($url,[
            'town_name' => $city_data['country'].'/'.$city_data['state'].'/'.$city_data['name'],
            'longitude' => $city_data['longitude'],
            'latitude' => $city_data['latitude'],
        ]);
        info($response);

        if ($response->failed())
            return false;
        return true;
    }

    use HasFactory;
}
