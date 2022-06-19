<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;

class DashboardApi extends Controller
{
    private $sufix     = null;
    private $api_info  = null;
    private $response  = null;
    private $data      = [];
    private $type      = null;

    public function __construct($sufix=null)
    {
        $this->sufix = $sufix;
        return $this;
    }

    public function make($type,$params=[])
    {   
            
        $this->api_info = config("dashboard_api.$type");
        $this->type = $type;
        $this->api_info["route"] .= $this->sufix ? "/".$this->sufix : "";
        $this->data = [
            "json"    => $params
        ];
        debug_log("Dashboard/Api", "Montou um request DashboardApi do tipo $type", [$params]);
        return $this;
    }

    public function send()
    {
        $client = new Client();
        $request   = $client->request($this->api_info["method"], $this->api_info["route"], $this->data);
        $response  = $request->getBody();
        $this->response = json_decode($response, true);
        $type = $this->type;
        debug_log("Dashboard/Api", "Enviou um request DashboardApi do tipo $type", [$response]);
        return $this;
    }
    
    public function getResponse()
    {
        return $this->response;
    }

    public function getData()
    {
        return $this->data;
    }
}