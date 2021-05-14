<?php

namespace App\Listeners;

use App\Models\Client;
use App\Events\ClientSaved;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CacheLatLangForClient
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ClientSaved $event)
    {
        /* we will only cache the latlng on creation for now. */
        if ($event->isUpdate) {
            return;
        }

        $this->saveLatLng($event->client);
    }


    public function saveLatLng(Client $client)
    {
        /* Improvements: latLng can be a dto */
        $latLng = $this->getLatLng($client);

        if (is_array($latLng) && isset($latLng['lat'])) {

            $client->latitude = $latLng['lat'];
            $client->longitude = $latLng['lng'];
            $client->save();
        }
    }


    public function getLatLng(Client $client)
    {
        /* get data from redis first */
        $data = Redis::get($client->latLngCacheKey());
        // $data = null;

        if (is_null($data)) {
            /* Improvements: We can inject a service to fetch lat, lng for a address
            and use it instead of making another method */
            $data = $this->cacheLatLng($client);
        }

        return $data;
    }

    public function cacheLatLng(Client $client)
    {
        /* we can include country if needed */
        $fullAddress = implode(", ", [$client->address1, $client->city, $client->state, $client->zip]);
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';
        $response = Http::get($url, [
            'address' => $fullAddress,
            'key' => config('services.google.map_key')
        ]);

        $data = $response->json();
        // dd($data);
        if (isset($data['results']) && isset($data['results'][0])) {

            $result = $data['results'][0];

            if (isset($result['geometry']) && isset($result['geometry']['location'])) {
                /* improvement: use dto here, current value will have lat and lng keys */
                $latLng = $result['geometry']['location'];

                /* cache data to redis */
                Redis::set($client->latLngCacheKey(), json_encode($latLng));

                /* return the latlng */
                return $latLng;
            }
        }

        return null;
    }
}
