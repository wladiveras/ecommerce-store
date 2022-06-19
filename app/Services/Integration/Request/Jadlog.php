<?php

	namespace App\Services\Integration\Request;
	
	use App\Models\Order as OrderModel;
	use App\Services\Integration\Models\Jadlog;
	use App\Services\Integration\Translate;
	use App\Services\Integration\Response\Response;

	class JadlogRequest
	{
		
		function __construct($data) 
		{
			 $this->make($data);
		}

		
		private function make($data)
		{	
			$this->data = new Jadlog();
			
			$this->data->conteudo   = (isset($data["conteudo"])) ? $data["conteudo"] : null;
			
			$this->data->pedido = []; 
			if(isset($data['orders']))
			{	
				foreach($data['orders'] as $order)
				{
					$this->data->pedido[] =  $order;
				}	
			}

	
			$this->data->totPeso =  (isset($data["totPeso"])) ? $data["totPeso"] : null;
			$this->data->totValor =  (isset($data["totValor"])) ? $data["totValor"] : null;
			$this->data->obs =  (isset($data["obs"])) ? $data["obs"] : null;
			$this->data->modalidade =  (isset($data["modalidade"])) ? $data["modalidade"] : null; 
			$this->data->contaCorrente = config('jadlog.conta');
			$this->data->tpColeta =  (isset($data["tpColeta"])) ? $data["tpColeta"] : null;
			$this->data->tipoFrete =  (isset($data["tipoFrete"])) ? $data["tipoFrete"] : null;
			$this->data->cdUnidadeOri =  "1344";
			$this->data->cdUnidadeDes =  (isset($data["cdUnidadeDes"])) ? $data["cdUnidadeDes"] : null;
			$this->data->cdPickupOri =  (isset($data["cdPickupOri"])) ? $data["cdPickupOri"] : null;
			$this->data->cdPickupDes =  (isset($data["cdPickupDes"])) ? $data["cdPickupDes"] : null;
			$this->data->nrContrato =  config('jadlog.contrato'); 
			$this->data->servico =  (isset($data["servico"])) ? $data["servico"] : null;
			$this->data->shipmentId =  (isset($data["shipmentId"])) ? $data["shipmentId"] : null;
			$this->data->vlColeta =  (isset($data["vlColeta"])) ? $data["vlColeta"] : null;
			

			$this->data->rem["nome"] = (isset($data["rem"]["nome"])) ? $data["rem"]["nome"] : null;
			$this->data->rem["cnpjCpf"] = (isset($data["rem"]["cnpjCpf"])) ? $data["rem"]["cnpjCpf"] : null;
			$this->data->rem["ie"] = (isset($data["rem"]["ie"])) ? $data["rem"]["ie"] : null;
			$this->data->rem["endereco"] = (isset($data["rem"]["endereco"])) ? $data["rem"]["endereco"] : null;
			$this->data->rem["numero"] = (isset($data["rem"]["numero"])) ? $data["rem"]["numero"] : null;
			$this->data->rem["compl"] = (isset($data["rem"]["compl"])) ? $data["rem"]["compl"] : null;
			$this->data->rem["bairro"] = (isset($data["rem"]["bairro"])) ? $data["rem"]["bairro"] : null;
			$this->data->rem["cidade"] = (isset($data["rem"]["cidade"])) ? $data["rem"]["cidade"] : null;
			$this->data->rem["uf"] = (isset($data["rem"]["uf"])) ? $data["rem"]["uf"] : null;
			$this->data->rem["cep"] = (isset($data["rem"]["cep"])) ? $data["rem"]["cep"] : null;
			$this->data->rem["fone"] = (isset($data["rem"]["fone"])) ? $data["rem"]["fone"] : null;
			$this->data->rem["email"] = (isset($data["rem"]["email"])) ? $data["rem"]["email"] : null;
			$this->data->rem["contato"] = (isset($data["rem"]["contato"])) ? $data["rem"]["contato"] : null;
			

			$this->data->des["nome"] = (isset($data["des"]["nome"])) ? $data["des"]["nome"] : null;
			$this->data->des["cnpjCpf"] = (isset($data["des"]["cnpjCpf"])) ? $data["des"]["cnpjCpf"] : null;
			$this->data->des["ie"] = (isset($data["des"]["ie"])) ? $data["des"]["ie"] : null;
			$this->data->des["endereco"] = (isset($data["des"]["endereco"])) ? $data["des"]["endereco"] : null;
			$this->data->des["numero"] = (isset($data["des"]["numero"])) ? $data["des"]["numero"] : null;
			$this->data->des["compl"] = (isset($data["des"]["compl"])) ? $data["des"]["compl"] : null;
			$this->data->des["bairro"] = (isset($data["des"]["bairro"])) ? $data["des"]["bairro"] : null;
			$this->data->des["cidade"] = (isset($data["des"]["cidade"])) ? $data["des"]["cidade"] : null;
			$this->data->des["uf"] = (isset($data["des"]["uf"])) ? $data["des"]["uf"] : null;
			$this->data->des["cep"] = (isset($data["des"]["cep"])) ? $data["des"]["cep"] : null;
			$this->data->des["fone"] = (isset($data["des"]["fone"])) ? $data["des"]["fone"] : null;
			$this->data->des["email"] = (isset($data["des"]["email"])) ? $data["des"]["email"] : null;
			$this->data->des["contato"] = (isset($data["des"]["contato"])) ? $data["des"]["contato"] : null;
			

			if(isset($data["dfe"]))
			{	
				
				foreach($data["dfe"] as $key => $dfe)
				{	
						$dfeValues[$key]["cfop"]            = $dfe["cfop"];
						$dfeValues[$key]["danfeCte"]        = $dfe["danfeCte"];
						$dfeValues[$key]["nrDoc"]   		  = $dfe["nrDoc"];
						$dfeValues[$key]["serie"]           = $dfe["serie"];
						$dfeValues[$key]["tpDocumento"]     = $dfe["tpDocumento"];		
						$dfeValues[$key]["valor"]           = $dfe["valor"];		
				}	
				
				$this->data->dfe = $dfeValues;
				
			}
				
		
			if(isset($data["volume"]))
			{	
				foreach($data["volume"] as $key => $volume)
				{	
						$volumeValues[$key]["altura"]           = $volume["altura"];
						$volumeValues[$key]["comprimento"]      = $volume["comprimento"];
						$volumeValues[$key]["identificador"]    = $volume["identificador"];
						$volumeValues[$key]["largura"]          = $volume["largura"];
						$volumeValues[$key]["peso"]             = $volume["peso"];		
				}	

				$this->data->volume = $volumeValues;
			
			}
			
			$this->data->frete["cepori"] = "25515126";
			$this->data->frete["cepdes"] = (isset($data["zip_code"])) ? $data["zip_code"] : null;
			$this->data->frete["peso"] = (isset($data["peso"])) ? (float)$data["peso"] : null;
			$this->data->frete["vldeclarado"] = (isset($data["vldeclarado"])) ? (float)$data["vldeclarado"] : null; 
			$this->data->frete["vlcoleta"] = (isset($data["vlcoleta"])) ? $data["vlcoleta"] : null;; 
			$this->data->frete["name"] = (isset($data["name"])) ? $data["name"] : null;
			$this->data->frete["street"] =(isset($data["street"])) ? $data["street"] : null;
			$this->data->frete["number"] = (isset($data["number"])) ? $data["number"] : null;
			$this->data->frete["district"] =  (isset($data["district"])) ? $data["district"] : null;
			$this->data->frete["state"] = (isset($data["state"])) ? $data["state"] : null;
			$this->data->frete["city"] = (isset($data["city"])) ? $data["city"] : null;
			$this->data->frete["complement"] = (isset($data["complement"])) ? $data["complement"] : null;
			$this->data->frete["reference"] = (isset($data["reference"])) ? $data["reference"] : null;
			
			$this->data->frete["cnpj"] = config('jadlog.cnpj');
			$this->data->frete["conta"] = config('jadlog.conta');
			$this->data->frete["contrato"] = config('jadlog.contrato');
			$this->data->frete["tpentrega"] = config('jadlog.tpentrega');
			$this->data->frete["tpseguro"] = config('jadlog.tpseguro');	

		
			if(isset($data["consulta"]))
			{		
				$values = $data["consulta"];
				$values = json_encode($values);
				$this->data->consulta = [								
						json_decode($values)			
				];
			}

	     }	
	}