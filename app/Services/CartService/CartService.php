<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CartService
{
	public  $request   = null;
	public  $data      = [];
	private $endpoint  = null;
	private $tokenType = null;
	private $username  = null;
	private $password  = null;
	private $method    = null;

	public function make($data, $action)
	{   
				$this->endpoint  = config('cart.url').$action;
				$this->tokenType = config('cart.tokenType');
				$this->username  = config('cart.username');
				$this->password  = config('cart.password');
				$this->method = ($action == "get") ?  "GET" : "PUT";
				$headers = [
                    // 'Authorization' => $this->tokenType . ' ' . base64_encode($this->username . ':' . $this->password),
                    'Content-Type'=> ' application/json'
				];
				
				$this->data = [
					"headers" => $headers,
					"json"    => $data
				];
				
		return $this;
	}

	public function send()
	{	
		try {
			$client = new Client();
            $guzzleReturn = $client->request($this->method, $this->endpoint, $this->data);
			// debug_log('Order/Systemcolor/New', 'Integração do tipo '.$this->integration_type.' enviada', [$this->data["json"]]);
			$result = $guzzleReturn->getBody()->getContents();
			// $result = json_decode($result, true);
			// debug_log('Order/Systemcolor/New', 'Resultado da integração', [$result, $this->data]);
			$status_code = $guzzleReturn->getStatusCode();
			return ["success" => true, "code" => $status_code, "message" => null, "data" => ["result" => $result, "request" => $this->data]];
		} catch (RequestException $e) {
            $result = $e->getMessage();
			// debug_log('Order/Systemcolor/New', 'Erro ao enviar nova Order', [$result, $this->data]);
			return ["success" => false, "code" => $e->getCode(), "message" => $e->getMessage(), "data" => ["result" => null, "request" => $this->data]];
		}
	}

	
}
