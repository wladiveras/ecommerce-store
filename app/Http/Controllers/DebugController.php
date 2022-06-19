<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Http\Controllers\ProductsController;
use ismaelgr\jadlog\JadlogClass;
use ismaelgr\getnet\services\Payment;
use App\Services\BusinessRule\ConfigProduct;
use App\Models\Sku;

class DebugController extends Controller
{

	public $createRequest = [

		"conteudo" => "PENDRIVE E MOUSE",
		"orders" => ["100123458", "000654323"],
		"totPeso" => 1,
		"totValor" => 25.52,
		"obs" => "OBS XXXXX",
		"modalidade" => 3,
		"contaCorrente" => "000001",
		"tpColeta" => "K",
		"tipoFrete" => 0,
		"cdUnidadeOri" => "1344",
		"cdUnidadeDes" => null,
		"cdPickupOri" => null,
		"cdPickupDes" => null,
		"nrContrato" => 12345,
		"servico" => 1,
		"shipmentId" => null,
		"vlColeta" => null,
		"rem" => [
			"nome" => "NOME DO REMETENTE",
			"cnpjCpf" => "00000000000000",
			"ie" => null,
			"endereco" => "RUA DO REMETENTE",
			"numero" => "123",
			"compl" => null,
			"bairro" => "BAIRRO",
			"cidade" => "SAO PAULO",
			"uf" => "SP",
			"cep" => "25515126",
			"fone" => "11 99999999",
			"cel" => "11 999999999",
			"email" => "email@jremetente.com.br",
			"contato" => "NOME CONTATO"
		],

		"des" => [
			"nome" => "NOME DO DESTINATARIO",
			"cnpjCpf" => "00000000000000",
			"ie" => null,
			"endereco" => "Av. Mascote",
			"numero" => "856",
			"compl" => null,
			"bairro" => "Vila Mascote",
			"cidade" => "São Paulo",
			"uf" => "SP",
			"cep" => "04363001",
			"fone" => "11 99999999",
			"cel" => "11 999999999",
			"email" => "email@destinatario.com.br",

			"contato" => "NOME CONTATO"
		],

		"dfe" => [
			[
				"cfop" => "6909",
				"danfeCte" => "00000000000000000000000000000000000000000000",
				"nrDoc" => "00000000",
				"serie" => "0",
				"tpDocumento" => 2,
				"valor" => 20.2
			],
			[
				"cfop" => "6909",
				"danfeCte" => "00000000000000000000000000000000000000000000",
				"nrDoc" => "00000000",
				"serie" => "0",
				"tpDocumento" => 2,
				"valor" => 20.2
			]
		],

		"volume" => [
			[
				"altura" => 10,
				"comprimento" => 10,
				"identificador" => "1234567890",
				"largura" => 10,
				"peso" => 1.0
			],

			[
				"altura" => 20,
				"comprimento" => 20,
				"identificador" => "0987654321",
				"largura" => 10,
				"peso" => 1.0
			],
		]

	];


	public $trackingRequest =  [

		// "consulta" => [ "codigo"=>"12345670"]
		"consulta" => ["shipmentId" => "00000000000000"]
	];

	public function dashboardApi() 
	{
		$dash_request = new DashboardApi("order_id");
		$dash_request->make("order_cancel",[
			"cancel_reason" => "lorem ipsum",
			"item_id"       => "order_item_id"
		]);
		$dash_request->send();
		dd($dash_request->getResponse());
	}

	public function createJadlog()
	{
		$obj  = new JadlogClass();
		return $obj->getData($this->createRequest, 'create');
		//  return $obj->getData($this->trackingRequest,'tracking');  
	}

	public function cancelCreditCard()
	{

		$paymentId = "";
		$amount = 0;
		$request['action'] = 3;
		$payment = new Payment();

		$result = $payment->makePayment($request);
		dd($result);
	}


	public function saveCreditCard()
	{

		$request['action'] = 4;
		$request['brand'] = 'Mastercard';
		$request['cardholderName'] = 'JOAO DA SILVA';
		$request['expirationMonth'] = '12';
		$request['expirationYear'] = '20';
		$request['cardholderIdentification'] = '12345678912';
		$request['verifyCard'] = false;
		$request['securityCode'] = '123';
		$request['customerId'] = 'customer_123';

		$payment = new Payment();

		$result = $payment->makePayment($request);
		dd($result);
	}

	public function safeBox()
	{

		$request['action'] = 5;
		$request['cardId'] = '9bae7a25-3434-4931-8a5f-4b953f3f539b';
		$request['cardAction'] = "getCard";

		$payment = new Payment();

		$result = $payment->makePayment($request);
		dd($result);
	}



	public function safeBoxListAll()
	{

		$request['action'] = 6;
		$request['customerId'] = 'customer_123';

		$payment = new Payment();

		$result = $payment->makePayment($request);
		dd($result);
	}

	public function businessRule()
	{
		$rule = new ConfigProduct();
		$options =  [
			[
				 "rule"   => "300",
				 "amount" => "300",
				 "sku_id" => 354,
				 "sku"    => Sku::find(354)->toArray(),
				 "fvalue" => "300"
			],
			[
				"rule"   => "200",
				"amount" => "200",
				"sku_id" => 353,
				"sku"    => Sku::find(353)->toArray(),
				"fvalue" => "200"
		   ],
		   [
				"rule"   => "100",
				"amount" => "100",
				"sku_id" => 352,
				"sku"    => Sku::find(352)->toArray(),
				"fvalue" => "100"
		],
		];
		$targetQty = 500;
		$combinations = $rule->getOrderMinPriceCombination($options,$targetQty);
		dd($combinations);
	}


	public function  emailTemplate() 
	{	
		return view('vendor.notifications.email');
	}
}
