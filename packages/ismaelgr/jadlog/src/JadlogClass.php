<?php

/*
* This file is part of jadlog-api-php.
*
* (c) Ismael Gonçalves <ecommerce@padraocolor.com.br>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace ismaelgr\jadlog;

use GuzzleHttp\Client;
use App\Services\Integration\Core;
use Illuminate\Support\Facades\Log;

class JadlogClass {


	private $username;
	private $password;
	private $cnpj;
	private $authString;
	private $url;
	private $endpoint;
	private $tokenType;
	private $request;


	/**
	* Cria uma nova instância do Jadlog API PHP.
	*
	* @param array $options
	*
	* @return void
	*/
	public function __construct()
	{
		$this->username = config('jadlog.username');
		$this->password   = config('jadlog.password');
		$this->cnpj   = config('jadlog.cnpj');
		$this->url   = config('jadlog.url');
		$this->tokenType = config('jadlog.tokenType');

	}

	public function prepareRequest($data){
		
		$jadlogObj = new Core();
		$response = $jadlogObj->make('jadlog',$data);
		$request = $response;

		return $request;
	}


	private function getAuthString()
    {
		$this->authString = config('jadlog.authString');
        return $this->authString;
    }


	private function getAction($action)
    {	
        switch ($action)
        {
            case 'create':
                $this->endpoint = config('jadlog.create');
            break;

            case 'cancel':
                $this->endpoint = config('jadlog.cancel');
            break;

            case 'tracking':
                $this->endpoint = config('jadlog.tracking');
			break;

			case 'shipping':
                $this->endpoint = config('jadlog.shipping');
            break;

			case 'xml':
                $this->endpoint = config('jadlog.xml');
            break;

			case 'pickup':
                $this->endpoint = config('jadlog.pickup');
            break;
        }

        return $this->endpoint;
    }

	/**
	* Metódo Responsável por fazer as Requisições na Jadlog
	*
	*
	*/

	public function getData ($request, $action) {
		
		
		$obj = $this->prepareRequest($request);
		$this->request = $obj->request->data;
		$this->authString = $this->getAuthString();
		$this->endpoint = $this->getAction($action);
		$url = $this->url.$this->endpoint;
		Log::debug('###################url->  '.$url );
		$client = new Client();

		$headers = [
			"Authorization" => $this->tokenType." ".$this->getAuthString(),
			"Content-Type"=> "application/json"
		];


		try {	

				$params = $this->request;
				
				$params = json_encode($params);	
			   	$params = json_decode($params);
			
				$array["headers"] = $headers;
				$array["json"] = $params;						
					
				$guzzleReturn = $client->request("POST", $url, $array);
				$response = json_decode($guzzleReturn->getBody(), true);
				debug_log("Jadlog", $action, [$request, $response,"IP: ". request()->ip()]);
		
		} catch (RequestException $e) {
		
			return $e->getCode(). $e->getMessage();
		}
		
		return $response;
	}

	public function getDataPickup ($request, $action, $zip_code) {
		
		
		$obj = $this->prepareRequest($request);
		$this->request = $obj->request->data;
		$this->authString = $this->getAuthString();
		$this->endpoint = $this->getAction($action);
		$url = $this->url.$this->endpoint.'/'.$zip_code;
		$client = new Client();

		$headers = [
			"Authorization" => $this->tokenType." ".$this->getAuthString(),
			"Content-Type"=> "application/json"
		];


		try {	

				$params = $this->request;
				
				$params = json_encode($params);	
			   	$params = json_decode($params);
			
				$array["headers"] = $headers;
				$array["json"] = $params;						
					
				$guzzleReturn = $client->request("POST", $url, $array);
				$response = json_decode($guzzleReturn->getBody(), true);
				debug_log("Jadlog", $action, [$request, $response,"IP: ". request()->ip()]);
		
		} catch (RequestException $e) {
		
			return $e->getCode(). $e->getMessage();
		}
		
		return $response;
	}


	private static $modals = [
		0 => [ "cod"=>3 ,"name" =>"Normal", "time"=>"até 5 dias úteis", "rawTime" => 5],
		1 => [ "cod"=>3 ,"name" =>"Rápida", "time"=>"até 3 dias úteis", "rawTime" => 3],
		2 => [ "cod"=>9 ,"name" =>"Expressa", "time"=>"até 2 dias úteis", "rawTime" => 2],
		11 => [ "cod"=>40 ,"name" =>"Pickup", "time"=>"até 2 dias úteis", "rawTime" => 2]
	];

	public static function getShippingModals(){
		return self::$modals;
	}

	private static $modalsPickup = [
		11 => [ "cod"=>40 ,"name" =>"Pickup", "time"=>"até 2 dias úteis", "rawTime" => 2]
	];

	public static function getShippingModalsPickup(){
		return self::$modalsPickup;
	}

	/**
	* Calcula Frete.
	*
	* @var array $options
	*
	* @return array
	*/
	public function shippingCalc($request, $refID=null, $only = [1,2]) {

		$obj = $this->prepareRequest($request);
		$this->request = $obj->request->data;
		$this->authString = $this->getAuthString();
		$this->endpoint = $this->getAction('shipping');
		$url = $this->url.$this->endpoint.$refID;

		$client = new Client();

		$headers = [
			"Authorization" => $this->tokenType." ".$this->getAuthString(),
			"Content-Type"=> "application/json"
		];

		$modals = array_only(self::$modals,$only);

		try {
			$start = microtime(true);
			foreach($modals as $key => $modal)
			 {
				$values = [
					"cepori"=> $this->request->frete['cepori'],
					"cepdes"=> $this->request->frete['cepdes'],	
					"frap"=> null,
					"peso"=> $this->request->frete['peso'] > 0.1 ? $this->request->frete['peso'] : 0.1,
					"cnpj"=> $this->cnpj, 
					"conta"=> "0140572",
					"contrato"=> "0140572",
					"tpentrega"=> "D",
					"tpseguro"=> "N",
					"vldeclarado"=>  $this->request->frete['vldeclarado'],
					"vlcoleta"=>  null,
					"modalidade" => $modal["cod"]
				];

				$values = json_encode($values);
				$params = [
					"frete"=> [
						json_decode($values)
					]
				];

				$params = json_encode($params);
				$params = json_decode($params);


				$array["headers"] = $headers;
				$array["json"] = $params;

				
				$guzzleReturn = $client->request("POST", $url, $array);
				Log::debug('guzzleReturn->  '.json_encode($guzzleReturn));
				$response[$key] = json_decode($guzzleReturn->getBody(), true);

				$profit = 0;
				$price = $response[$key]["frete"][0]["vltotal"];
				if(@$this->request->frete['apply_profit'])
				{
					$profit = ($price / 100) * 15; 
				}
				
				
				$totalPrice = $price + $profit; 
				
				$calcs[$key]["name"] =  $modal["name"];
				$calcs[$key]["time"] =  $modal["time"];
				$calcs[$key]["rawTime"] =  $modal["rawTime"];
				$calcs[$key]["price"] = "R$ ".number_format($totalPrice, 2, ',', '.');
				$calcs[$key]["rawPrice"] =  $totalPrice;
				
			}
			$ttt = microtime(true) - $start;
			$result = [
				'calcs' => $calcs,
				'time' => $ttt
			];

			
		} catch (RequestException $e) {

			return $e->getCode(). $e->getMessage();
		}

		return $result;
	}

	public function shippingCalcPickup($request, $refID=null, $only = [11]) {

		$obj = $this->prepareRequest($request);
		$this->request = $obj->request->data;
		$this->authString = $this->getAuthString();
		$this->endpoint = $this->getAction('shipping');
		$url = $this->url.$this->endpoint.$refID;

		$client = new Client();

		$headers = [
			"Authorization" => $this->tokenType." ".$this->getAuthString(),
			"Content-Type"=> "application/json"
		];

		$modals = array_only(self::$modals,$only);

		try {
			$start = microtime(true);
			foreach($modals as $key => $modal)
			 {
				$values = [
					"cepori"=> $this->request->frete['cepori'],
					"cepdes"=> $this->request->frete['cepdes'],	
					"frap"=> null,
					"peso"=> $this->request->frete['peso'] > 0.1 ? $this->request->frete['peso'] : 0.1,
					"cnpj"=> $this->cnpj, 
					"conta"=> "0140572",
					"contrato"=> "0140572",
					"tpentrega"=> "D",
					"tpseguro"=> "N",
					"vldeclarado"=>  $this->request->frete['vldeclarado'],
					"vlcoleta"=>  null,
					"modalidade" => $modal["cod"]
				];

				$values = json_encode($values);
				$params = [
					"frete"=> [
						json_decode($values)
					]
				];

				$params = json_encode($params);
				$params = json_decode($params);


				$array["headers"] = $headers;
				$array["json"] = $params;

				
				$guzzleReturn = $client->request("POST", $url, $array);
				Log::debug('guzzleReturn->  '.json_encode($guzzleReturn));
				$response[$key] = json_decode($guzzleReturn->getBody(), true);

				$profit = 0;
				$price = $response[$key]["frete"][0]["vltotal"];
				if(@$this->request->frete['apply_profit'])
				{
					$profit = ($price / 100) * 15; 
				}
				
				
				$totalPrice = $price + $profit; 
				
				$calcs[$key]["name"] =  $modal["name"];
				$calcs[$key]["time"] =  $modal["time"];
				$calcs[$key]["rawTime"] =  $modal["rawTime"];
				$calcs[$key]["price"] = "R$ ".number_format($totalPrice, 2, ',', '.');
				$calcs[$key]["rawPrice"] =  $totalPrice;
				
			}
			$ttt = microtime(true) - $start;
			$result = [
				'calcs' => $calcs,
				'time' => $ttt
			];

			
		} catch (RequestException $e) {

			return $e->getCode(). $e->getMessage();
		}

		return $result;
	}

}
