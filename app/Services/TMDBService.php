<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;

class TMDBService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
    }

    public function get($endpoint, $params = [])
    {
        $params['api_key'] = $this->apiKey;
        $response = Http::get("https://api.themoviedb.org/3/$endpoint", $params);
        return $response->json();
    }
}
