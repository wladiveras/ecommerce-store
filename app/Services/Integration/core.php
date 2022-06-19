<?php

	namespace App\Services\Integration;
	use App\Services\Integration\Request\JadlogRequest;
	use GuzzleHttp\Client;
	use GuzzleHttp\Exception\RequestException;

	class Core {
	
		public $request  = null;
		
		public function make($value,$data = []) {
		
			switch($value)
			{
				
				case "jadlog" :	
				
					$this->request = new JadlogRequest($data);
					
				break;

				default : 
					$this->request = "$value é um valor inválido para INTEGRATION";
				break;
			}
			return $this;
		}

	}
