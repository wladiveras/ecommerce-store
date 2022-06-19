<?php
namespace App\Http\Controllers;
use GuzzleHttp\Client;

class FreteCalcController extends Controller
{
    private $client = null;

    public function __construct()
    {
        $this->client = new Client();
    }

    private function makeAuthData()
    {
        return [
            "headers" => config("fretecalc.auth")
        ];
    }

    private function makeCalculateData($auth,$declared_price,$weight,$zip_code)
    {   
        
        return [
            "headers" => [
                "token"  =>  $auth["token"]
            ],
            "json" => [
                "declared_price"  =>  $declared_price,
                "weight"          =>  $weight,
                "zip_code"        =>  $zip_code,
                "apply_profit"    =>  false
            ]
        ];
    }

    private function getAuth() 
    {
        $data = $this->makeAuthData();
        $url = config("fretecalc.endpoint.getAuth");
        $guzzleReturn = $this->client->request("GET", $url, $data);
        $response = json_decode($guzzleReturn->getBody(), true);
        return $response["data"];
    }

    public function requestApiFrete($declared_price,$weight,$zip_code,$process=false) 
    {
        $auth = $this->getAuth();
        $data = $this->makeCalculateData($auth,$declared_price,$weight,$zip_code);
        $url = config("fretecalc.endpoint.calculate");
        $guzzleReturn = $this->client->request("GET", $url, $data);
        $response = json_decode($guzzleReturn->getBody(), true);
        return $process ? $this->process($response) : $response;
    }

    private function process($response)
    {
        $data = $response["data"];
        $package_price = $data["package"]["price"];
        $package_time  = $data["package"]["time"];
        $normal = [
            "name"     => "Normal",
            "rawPrice" => floatval($package_price),
            "price"    => "R$ ".str_replace(".",",",number_format($package_price,2)),
            "rawTime"  => intval($package_time)
        ];

        $rapido = [
            "name"     => "Rápido",
            "rawPrice" => floatval($package_price),
            "price"    => "R$ ".str_replace(".",",",number_format($package_price,2)),
            "rawTime"  => intval($package_time)
        ];

        $expresso_price = $data[".com"]["price"];
        $expresso_time  = $data[".com"]["time"];
        $expresso = [
            "name"     => "Expresso",
            "rawPrice" => floatval($expresso_price),
            "price"    => "R$ ".str_replace(".",",",number_format($expresso_price,2)),
            "rawTime"  => intval($expresso_time)
        ];
        return [$normal,$rapido,$expresso];
    }
    

}

    

