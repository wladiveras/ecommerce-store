<?php

namespace App\Http\Controllers;

use Cart;
use App;
use App\Models\Sku;
use App\Services\PDFService;
use App\Models\lista_desejos\ListaDesejo;
use Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CartResource;
use Illuminate\Support\Arr;
use App\Models\Product;

class ListaDeDesejosController extends Controller{

    public $cart = null;

    public function init($userCode)
    {
        return $this->cart = Cart::session($userCode);
    }

    public function show()
    {
        $user = Auth::user();
        //$cart = $this->init($user->code);
        $cart1 = ListaDesejo::where(['id_user' => $user->id])->pluck('cart')->all();
        $data = [
            'user' => $user,
            'cart' => (object)$this->prepareForCart($cart1),
            'not_empty' => true,
        ];
        Log::debug('data->  '.gettype($data["cart"]));
        return view('pages.lista_de_desejos.show', $data);
    }

    public function remove($id)
    {
        $lista = ListaDesejo::where(['id_cart' => $id]);
        $lista->delete();
        return redirect(route('lista_desejo.view'));
    }

    public function prepareForCart($cart1)
    {
        return $this->toArray($cart1);
    }

    public function toArray($cart1)
    {
        $cart_item = array();
        foreach ($cart1 as $key => $value) {
            $array = json_decode($value);
            $this->item = $array->attributes;
            $this->data = $this->item->data;
            $cart_item['item'][]  = array(
                //"item" => $array->attributes,
                "id_cart"=>$array->id,
                "data" => $this->item->data,
                "name" => $array->name,
                "price" => $array->price,
                "quantity" => $this->getQuantity($array->quantity),
                "options" => $this->getOptions(),
                "files" => $this->getFiles($key),
                "product" => $this->getProduct($key),
                "prepared_in" => $this->getPreparedIn(),
                "skus" => $this->item->items
            );
            $this->setAdditionalOptions($cart_item);
            $this->setSizes($cart_item);
            $this->setMeasures($cart_item);
            $this->setCover($cart_item);
            $this->setFinishes($cart_item);
        }
        //Log::debug('cart_item->  '.json_encode($cart_item));
        return $cart_item;
    }

    private function getPreparedIn(){
        $days = $this->item->prepared_in;
        return "em até $days ".str_plural("dia",$days).($days > 1 ? " úteis" : " útil");
    }

    private function getFiles($key){
        Log::debug('upload->  '.json_encode($this->item->items[0]->upload->file));
        $files = $this->item->items[0]->upload->file;
        if($files){
            return $files;
        }else{
            return $this->item->items[0];
        }
       
    }

    private function setSizes(&$array){
        $sizes = @$this->data->sizes;
        if($sizes){
            $sizes = array_map(function($v){
                return [
                    'label' => $v['label'],
                    'quantity' => "{$v['qty']} ". str_plural('unidade',$v['qty'])
                ];
            },$sizes);
            $array['sizes'] = array_chunk($sizes,2);
        }
    }

    private function getQuantity($quantity){
        $quantity = number_format($quantity,0,",",".");
        $plural = str_plural("unidade",$quantity);
        return "$quantity $plural";
    }
    
    private function getProduct($key){
        return $this->item->items[0]->product;
    }

    private function getOptions(){
        return array_filter((array)$this->data->options, function($v){
            return ($v !== null) || (trim($v) != "");
        });
    }

    private function setCover(&$array){
        $cover = @$this->data->extra->cover;
        
        if($cover){
            $array['cover'] = $cover." g/m²";
        }
    }

    private function setFinishes(&$array){
        $finishes = $this->item->finishes;
        if($finishes){
            $array['finishes'] = $finishes['finishes'];
            $array['finishesTotal'] = array_except($finishes,['finishes']);
        }
    }

    private function setAdditionalOptions(&$array){
        $additional = @$this->data->additional;
        if($additional){
            $array['additionalConfig'] = $additional;
        }
    }

    //======================================= Measures
    private function setMeasures(&$array){
        $measures = $this->getMeasures();
        if($measures){
            $array['measures'] = $measures;
            $total = $this->getTotalMeasures($measures);
            $array['totalMeasures'] = $total;
            $array['measuresText'] = $this->getMeasuresText($total,$measures);
        }
    }

    private function format_number($n,$decimals = 2){
        if(is_numeric($n)){
            return number_format($n,$decimals);
        }else{
            return $n;
        }
    }

    private function getMeasures(){
        $measures = @$this->data->measures;

        foreach($measures as $val){
            if($val === null) return;
        }

        return $measures;
    }

    private function getTotalMeasures($measures){
        extract((array)$measures);
        $res = $width * $height;
        if($res < 0.09){
            return $this->format_number($res,4);
        }
        return $this->format_number($res);
    }

    private function getMeasuresText($total,$measures){
        $measures = array_map([$this,'format_number'],(array)$measures);
        extract($measures);

        return "$total {$unit}² ($width$unit x $height$unit)";
    }

    //==================================== Measures

    public function addCart($id)
    {
        $data = ListaDesejo::where(['id_cart' => $id])->pluck('cart')->all();
        foreach ($data as $key => $value) {
            $array = json_decode($value);
            
            $product = Product::findOrFail($array->attributes->items[0]->product->id);
            $additional = $this->get_additional_price(@array_filter($data["additional"], 'strlen'), $product);
            $_sku = ["price" => 0, "id" => null];
            //foreach ($array->attributes->items[0] as $_row) {
               
                if ($array->attributes->items[0]->sku->reseller_price > $_sku["price"]) {
                    $_sku = [
                        "id" => $array->attributes->items[0]->sku->id,
                        "sku_qty" => $array->attributes->items[0]->quantity,
                        "price" => $array->attributes->items[0]->sku->reseller_price,
                    ];
                }
            //}
            $idSku = $array->attributes->items[0]->sku->id;
            $SkuObj = $array->attributes->items[0]->sku;
            $upload = $array->attributes->items[0]->upload;
            
            $qty = $array->attributes->items[0]->quantity;
            $finishes = $this->get_finishes_price($_sku["id"], 
            $array->attributes->finishes, $array->attributes->data->requests, 
            $array->attributes->data->order_qty, $_sku["sku_qty"]);
            $cart_id = $id;
            $data["finishes"] = $finishes;
            $data["additional"] = $additional;
            $skus = $SkuObj;
            $order = [];
            unset($array->attributes->items[0]->sku, $array->attributes->items[0]->sku->product_id);
            $user = Auth::user()->toArray();
            $cartController = App::make(CartController::class);
            $cart = $cartController->init($user['code']);
            
            foreach ($skus as $row) {
                
                $sku = Sku::find($idSku);
                $aux = [
                    "sku"        =>   $sku->toArray(),
                    "upload"     =>   $upload,
                    "quantity"   =>   $qty,
                    "price"      =>   (float) $sku->reseller_price,
                ];
                $items[] = $aux;
            }
    
            $finishes = ($finishes ? $finishes : null);
            $additional = ($additional ? $additional : null);
            $total = $this->get_order_price($skus, $finishes, $additional, $array->attributes->data->measures,$qty);
            $order["order"] = [
                "total" => $total,
                "qty"   => (float) $array->attributes->data->order_qty,
                "data"  => (array)$array->attributes,
                "date"  => date("Y-m-d"),
                "time"  => date("H:i:s"),
                "additional" => $additional,
                "finishes" => $finishes,
                "items" => $items
            ];
            $cart_item = $this->sku_to_cart($order, $cart_id);
            Log::debug('cart_item->  '.json_encode($cart_item));
            $cart->add($cart_item);
        }    
        return redirect(route('cart.index'));
    }

    public function sku_to_cart($skus, $group)
    {
        $result = [];
        $nome = "";
        foreach ($skus["order"]["data"] as $key => $value) {
            $nome = $value->ref_name;
            break;  
        }
        $cart =  [
            "id"        => $group,
            "quantity"  => $skus["order"]["qty"],
            "price"     => $skus["order"]["total"],
            "name"      => $nome,
            "attributes" => [
                "data" => $skus["order"]["data"],
                "prepared_in" => $this->calculate_preparation_time($skus),
                "date" => $skus["order"]["date"],
                "time" => $skus["order"]["time"],
                "total" => $skus["order"]["total"],
                "additional"  => $skus["order"]["additional"],
                "finishes"    => $skus["order"]["finishes"],
                "items"       => $skus["order"]["items"]
            ]
        ];
        //return $skus["order"]["finishes"];
        for ($i = 0; $i < count($cart["attributes"]["items"]); $i++) {
            $_sku = Sku::find($cart["attributes"]["items"][$i]["sku"]['id']);
            $cart["attributes"]["items"][$i]["product"] = $_sku->product->load("files")->toArray();
        }
        return $cart;
    }

    public function calculate_preparation_time($data)
    {
        $longest_sku_time = max(Arr::pluck($data['order']['items'], 'sku.prepared_in'));
        return $longest_sku_time + @$data['order']['finishes']['additional_time'];
    }

    private function get_finishes_price($sku_id, $finishes = [], $requests = [], $order_qty, $sku_qty)
    {
        $total = 0;
        $total_time = 0;
        if (!$finishes) return 0;

        $this->cartController =  new FinishesController();
        $_finishes = ["finishes" => [], "total" => 0];

        foreach ($finishes as $key => $value) {
            if (!$value['qty']) continue;
            $request = $requests[$key];
            $finish = Finish::where("finish_ref_id", $request["finish_ref_id"])->first();
            $result = $this->calculateFinishPrice($request, $finish, $sku_id, $order_qty, $sku_qty);
            $price = $result["additionalPrice"];

            $time  = floatval($result["additionalTime"]);
            $aux  = [];
            $aux["name"]  = $finish->name;
            $aux["price"] = $price;
            $aux["time"]  = $time;
            $aux["ref_id"] = $finish->finish_ref_id;
            $aux["qty"]   = $request["qty_finish"];
            $_finishes["finishes"][] = $aux;
            $total += $price;
            $total_time += $time;
        }
        $_finishes["total"]  = $total;
        $_finishes["additional_time"]  = $total_time;
        return $_finishes;
    }

    private function get_order_price($sku, $finishes, $additional, $measures = null,$qty)
    {
        $price = 0;
        Log::debug('measures->  '.json_encode($measures));
        //foreach ($skus as $sku) {
           
            $qty = (float) $qty;
            if (@$measures->height) {

                $height = floatval($measures->height);
                $width = floatval($measures->width);
                if (@$measures->unit == "cm") {
                    $height = $height / 100;
                    $width = $width / 100;
                }
                $area = $height * $width;
                $aux = $area * $qty;
                if ($aux > 1) {
                    $price += ((float) $sku->reseller_price) * $aux;
                } else {
                    $price += ((float) $sku->reseller_price);
                }
            } else {
                $price += ((float) $sku->reseller_price * $qty);
            }
        //}
        $price = $this->sum_finishes($price, $finishes);
        $price = $this->sum_additional($price, $additional);
        return $price;
    }

    private function sum_additional($price, $additional)
    {
        $total = 0;
        if (!$additional) {
            return $price;
        }
        $total = (float) $additional["total"];
        return $total + $price;
    }

    private function sum_finishes($price, $finishes)
    {
        $has_corte_vinco = false;
        if (@$finishes["finishes"]) {
            for ($i = 0; $i < count($finishes["finishes"]); $i++) {
                if ($finishes["finishes"][$i]["name"] == "Corte e Vinco") {
                    $finishes["finishes"][$i]["additional_price"] = ["description" => "faca", "price" => 150];
                    $finishes["total"] += 150;
                    $has_corte_vinco = true;
                    break;
                }
            }
        }

        if (!$finishes) {
            return $price;
        }
        $total = (float) $finishes["total"];
        return $total + $price;
    }

    private function is_laminacao($finish)
    {
        return strpos(strtolower($finish->name), "lamina") > -1;
    }

    private function calculateFinishPrice($request, $finish, $sku_id, $order_qty, $sku_qty)
    {
        $finishResponse = $this->cartController->calcFinishes($sku_id, $request["finish_ref_id"], $request["department"], $request["qty_sku"], $request["qty_finish"]);

        $price = &$finishResponse["additionalPrice"];

        if ($finish->data["multiply"] == "sku") {
            $request["qty_finish"]   *= $order_qty;
            $price                   *= $sku_qty;
        } else {
            if ($finish->finish_ref_id == 4) $price += 150; //faca

            if ($this->is_laminacao($finish)) {
                $request["qty_finish"]   = $request["qty_finish"] * $order_qty;
                $price                   = $price * $order_qty;
            }
        }
        return $finishResponse;
    }

    private function get_additional_price($additional = [], $product)
    {

        $add = [];
        $add["total"] = 0;
        $add["additional_attributes"] = [];
        if (!$additional)
            return 0;
        if (!is_array($additional))
            return 0;
        foreach ($additional as $key => $value) {
            $_add = AdditionalConfig::findOrFail($key);
            $config = $_add->attr->where("id", $value)->first()->toArray();
            if ($_add->type = "CA")  $config["price"] = $this->getCaPrice($product, $config["price"]);
            $add["additional_attributes"][] = $config;
            $price = floatval($config["price"]);
            $add["total"] += $price;
        }
        return $add;
    }
}    