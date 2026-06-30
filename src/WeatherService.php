<?php

namespace PHP;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WeatherService
{
    private string $apiKey = 'a28191a4143c8a43cab77fe6c103219d';
    private string $apiEndpoint = 'https://home.openweathermap.org/api_keys';
    private Client $client;

    public function __construct() {
       
        $this->client = new Client(['allow_redirects' => false]);
    }

    public function getWeather(string $city): array
    {
        try {
            $response = $this->client->get($this->apiEndpoint, [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric'
                ]
            ]);

        
            if ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
                echo "Error: The API redirected to a login page. Your API key is likely inactive or wrong.\n";
                exit(1);
            }

            $rawBody = $response->getBody()->getContents();
            $data = json_decode($rawBody, true);

            if (empty($data) || !isset($data['main'])) {
                echo "Error: Received unparsable body data from the weather API.\n";
                exit(1);
            }

            return [
                'city' => $data['name'] ?? 'N/A',
                'temperature' => $data['main']['temp'] ?? 'N/A',
                'description' => $data['weather'][0]['description'] ?? 'N/A', // Fixed: Added [0] index wrapper
                'humidity' => $data['main']['humidity'] ?? 'N/A'
            ];

        } catch (RequestException $e) {
            echo "Network/API Connection Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}
