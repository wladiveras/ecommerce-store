<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ismaelgr\jadlog\JadlogClass;
use ismaelgr\jadlog\PickupClass;
use \Auth;
use App\Models\Sku;
use App\Http\Controllers\FreteCalcController as FreteCalc;
use Illuminate\Support\Facades\Log;

class JadlogController extends Controller
{
    private function calculate_shipping_time($shipping_options, $cart_time)
    {
        foreach ($shipping_options as &$option) {
            $option["rawTime"] += $cart_time;
            $option["time"] = preg_replace("/\d{1,}/", $option["rawTime"], $option["time"]);
        }
        return $shipping_options;
    }

    public function useFreteCalcApi($subtotal,$weight,$data,$cart_time)
    {
        $freteCalc = new FreteCalc();
        $result = $freteCalc->requestApiFrete($subtotal, $weight, $data['zip_code'],true);
        $result = array_map(function($x) use ($cart_time) {
            if($x["name"] == "Normal") $cart_time+=2;
            $x["rawTime"] = $x["rawTime"] + $cart_time;
            $x["time"] = "até ".$x["rawTime"]." dias úteis";
            return $x;
        },$result);
        return [ 'calcs' => $result ];
    }

    public function shipping(Request $data)
    {
        $user = \Auth::user();
        $cartController = \App::make(CartController::class);
        $cart = $cartController->init($user->code);
        $subtotal = array_sum(array_pluck($cart->getContent(), "price"));
        $weight = CartController::getCartWeight($cart);
        $cart_time = $cartController->longest_sku_preparation_time($cart);

        if (config("fretecalc.active")) return $this->useFreteCalcApi($subtotal,$weight,$data,$cart_time);

        $data = [
            "zip_code" => $data['zip_code'], // cep destinatário
            "peso" => $weight, // Peso total a ser transportado
            "vldeclarado" => $subtotal, //Valor declarado de Nota Fiscal
            "vlcoleta" => "null", // Valor de coleta negociado com Jadlog
        ];

        $data["subtotal"] = $subtotal;

        $jadlog = new JadlogClass();
        $shipping_calc = $jadlog->shippingCalc($data);
        $res = $shipping_calc;
        $res['calcs'] = $this->calculate_shipping_time($shipping_calc['calcs'], $cart_time);
        return $res;
    }

    public function shippingPickup(Request $data)
    {
        $user = \Auth::user();
        $cartController = \App::make(CartController::class);
        $cart = $cartController->init($user->code);
        $subtotal = array_sum(array_pluck($cart->getContent(), "price"));
        $weight = CartController::getCartWeight($cart);
        $cart_time = $cartController->longest_sku_preparation_time($cart);

        if (config("fretecalc.active")) return $this->useFreteCalcApi($subtotal,$weight,$data,$cart_time);

        $data = [
            "zip_code" => $data['zip_code'], // cep destinatário
            "peso" => $weight, // Peso total a ser transportado
            "vldeclarado" => $subtotal, //Valor declarado de Nota Fiscal
            "vlcoleta" => "null", // Valor de coleta negociado com Jadlog
        ];

        $data["subtotal"] = $subtotal;

        $jadlog = new JadlogClass();
        $shipping_calc = $jadlog->shippingCalcPickup($data);
        $res = $shipping_calc;
        $res['calcs'] = $this->calculate_shipping_time($shipping_calc['calcs'], $cart_time);
        return $res;
    }

    public function create(Request $data)
    {

        $jadlog = new JadlogClass();
        return $jadlog->getData($data, 'create');
    }

    public function pickup(Request $data,$zip_code)
    {
        
        $jadlog = new JadlogClass();
        $response = array();
        $jadlogReturn = $jadlog->getDataPickup($data, 'pickup',$zip_code);
        foreach($jadlogReturn as $array){
            foreach($array as $result){
                $object = (object) $result;
                $pickup = new PickupClass();
                $pickup->razao = $object->razao;
                foreach($object->pudoEnderecoList as $pudoEnderecoList){
                    $endereco = (object) $pudoEnderecoList;
                    $pickup->street = $endereco->endereco;
                    $pickup->number = $endereco->numero;
                    $pickup->district = $endereco->bairro;
                    $pickup->zip_code = $endereco->cep;
                    $pickup->state = $endereco->uf;
                    $pickup->city = $endereco->cidade;
                }
                
                $response[] = $pickup;
            }   
            
        }
        
        return $response;
    }


    public function cancel(Request $data)
    {

        $jadlog = new JadlogClass();
        return $jadlog->getData($data, 'cancel');
    }

    public function traking(Request $data)
    {
        $jadlog = new JadlogClass();
        return $jadlog->getData($data, 'traking');
    }

    public function getXml(Request $data)
    {
        $jadlog = new JadlogClass();
        return $jadlog->getData($data, 'xml');
    }

    public function generateId($id)
    {
        if (config('app.env') == 'production') {
            // $this->ref_id = substr(config('app.env'), 0, 4) . '_' . $id;
            $this->ref_id =  "id" . $id;
        } else {
            $this->ref_id = time();
        }
    }

    public function createJadlogData($data, $cart)
    {
        $this->generateId($data['orderId']);
        $orders[] = $this->ref_id;
        $conteudo = "";
        $totPeso = 0.0;
        $obs = "";

        foreach ($cart as $item) {
            $conteudo .= $item["product"]["name"] . ", ";
            $totPeso +=  $item["skus"][0]["sku"]["weight"];
        }

        $conteudo =  rtrim($conteudo, ", ");

        $i = 0;
        foreach ($cart as $item) {

            $id = $item["skus"][0]["sku"]["id"];
            $item = Sku::where('id', $id)->first();

            $volume[$i]["altura"]        =  1;
            $volume[$i]["comprimento"]   =  1;
            $volume[$i]["identificador"] =  "$item->id";
            $volume[$i]["largura"]       =  1;
            $volume[$i]["peso"]          =  $item->weight;

            $dfe[$i]["cfop"] = "5102";
            $dfe[$i]["danfeCte"] = "00000000000000000000000000000000000000000000";
            $dfe[$i]["nrDoc"] = "00000000";
            $dfe[$i]["serie"] = "0";
            $dfe[$i]["tpDocumento"] = 2;
            $dfe[$i]["valor"] = $item["product"]["base_price"];

            $i++;
        }

        $orderData["conteudo"] = $conteudo;
        $this->setVars($data['shipping_address']);
        $orderData = [

            "conteudo" => $orderData["conteudo"],

            "orders" => $orders,
            "totPeso" => $totPeso,
            "totValor" => $data["totalPrice"],
            "obs" => $obs,
            "modalidade" => @$data["shipping"]["method"] ? $data["shipping"]["method"] : 0,
            "contaCorrente" => "0140572",
            "tpColeta" => "K",
            "tipoFrete" => 0,
            "cdUnidadeOri" => "1",
            "cdUnidadeDes" => null,
            "cdPickupOri" => null,
            "cdPickupDes" => null,
            "nrContrato" => "0140572",
            "servico" => 1,
            "shipmentId" => null,
            "vlColeta" => null,
        ];

        $this->setSenderAddress($orderData, $this->rem_address);
        $this->setReceiverAddress($orderData, $data['shipping_address']);
        $orderData["dfe"] = $dfe;
        $orderData["volume"] = $volume;

        return  $orderData;
    }

    public function setVars($data)
    {
        $reseller = Auth::user()->reseller;

        $reseller_info = [
            "nome" => $reseller->full_name,
            "cnpjCpf" => $reseller->getOriginal('doc')
        ];

        if (isset($data["client_doc"])) { //envio direto para o cliente
            $receiver_data = [
                "nome" => @$data['name'],
                "cnpjCpf" => preg_replace("/\D+/", "", $data["client_doc"])
            ];
            $sender_data = $reseller_info;
        } else {
            $receiver_data = $reseller_info;
            $sender_data = [
                "nome" => "Padrão Color",
                "cnpjCpf" => "14572530000119"
            ];
        }

        $company = \App\Models\ShippingPost::where('api_ref', 'G')->first();
        $sender_address = $company->address;
        $this->rem_address = $sender_address;

        $this->des_data = $receiver_data;
        $this->rem_data = $sender_data;
    }

    public function setAddress(&$array, $address)
    {
        $array = array_merge([
            "nome" => @$address["name"],
            "endereco" => $address["street"],
            "numero" => $address["number"],
            "compl" => $address["complement"],
            "bairro" => $address["district"],
            "cidade" => $address["city"],
            "uf" => $address["state"],
            "cep" => $address["zip_code"],
        ], $array);
    }

    public function setAddressContact(&$array)
    {
        $reseller = Auth::user()->reseller;
        $array = array_merge([
            "fone" => $reseller->phone,
            "cel" => $reseller->phone,
            "email" => $reseller->email,
            "contato" => $reseller->name
        ], $array);
    }

    public function setSenderAddress(&$array, $data)
    {
        $array["rem"] = $this->rem_data;
        $this->setAddressCommonData($array["rem"]);
        $this->setAddressContact($array);
        $this->setAddress($array["rem"], $data);
    }

    public function setReceiverAddress(&$array, $data)
    {
        $array["des"] = $this->des_data;
        $this->setAddressCommonData($array["des"]);
        $this->setAddressContact($array);
        $this->setAddress($array["des"], $data);
    }

    public function setAddressCommonData(&$array)
    {
        $array = array_merge([
            "ie" => null
        ], $array);
    }
}
