<?php
namespace App\Service;

class IpGeolocation
{
    private $apiKey;
    
    public function __construct() {
        $this->apiKey = $_ENV['GEO_IP_KEY'];
    }

    public function getGeolocation(string $ip, string $lang = "en", string $fields = "*", string $excludes = "") {
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey=".$this->apiKey."&ip=".$ip."&lang=".$lang."&fields=".$fields."&excludes=".$excludes;
        $cURL = curl_init();

        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_HTTPGET, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: '.$_SERVER['HTTP_USER_AGENT']
        ));

        return curl_exec($cURL);
    }

    public function getContinent(string $ip): string|null
    {
        $response = $this->getGeolocation($ip, "en", "continent_code");
        $data = json_decode($response, true);
        return $data['continent_code'] ?? null;
    }
}