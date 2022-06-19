<?php

namespace App\Http\Controllers;

use Cart;
use App;
use App\Models\Sku;
use App\Services\PDFService;
use App\Models\lista_desejos\ListaDesejo;
use App\Models\OrderSku;
use Auth;
use DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CartResource;
use Illuminate\Support\Arr;
use App\Models\Product;

class ComprarNovamenteController extends Controller{

    public function comprar_novamente($order_id){

        $order = @OrderSku::where(['order_id' => $order_id])->get();
        foreach ($order as $key => $value) {
            
            $array = json_decode(json_encode($value->data));
            //Log::debug('order->  '.json_encode($array));
            $product = Product::findOrFail($array->sku->product->id);
            $additional = $this->get_additional_price(@array_filter($data["additional"], 'strlen'), $product);
            $_sku = ["price" => 0, "id" => null];
            //foreach ($array->attributes->items[0] as $_row) {
               
                if ($array->sku->reseller_price > $_sku["price"]) {
                    $_sku = [
                        "id" => $array->sku->id,
                        "sku_qty" => $array->quantity,
                        "price" => $array->sku->reseller_price,
                    ];
                }
            //}
            $idSku = $array->sku->id;
            $SkuObj = $array->sku;
            $upload = $array->config_info->upload;
            
            $qty = $array->quantity;
            $finishes = $this->get_finishes_price($_sku["id"], 
            $array->config_info->finishes, $array->config_info->requests, 
            $array->config_info->order_qty, $_sku["sku_qty"]);
            $cart_id = uniqid();
            $data["finishes"] = $finishes;
            $data["additional"] = $additional;
            $skus = $SkuObj;
            $order = [];
            unset($array->sku, $array->sku->product_id);
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
            $total = $this->get_order_price($skus, $finishes, $additional, $array->config_info->measures,$qty);
            $order["order"] = [
                "total" => $total,
                "qty"   => (float) $array->config_info->order_qty,
                "data"  => (array)$array,
                "date"  => date("Y-m-d"),
                "time"  => date("H:i:s"),
                "additional" => $additional,
                "finishes" => $finishes,
                "items" => $items
            ];
            $cart_item = $this->sku_to_cart($order, $cart_id);
            //Log::debug('$array->  '.json_encode($array));
            $cart->add($cart_item);
        }    
        return redirect(route('cart.index'));
    }

    public function sku_to_cart($skus, $group)
    {
        $result = [];
        //foreach ($skus["order"]["data"] as $key => $value) {
            //Log::debug('order->  '.json_encode($skus["order"]["data"]["config_info"]));
            $nome = $skus["order"]["data"]["config_info"]->ref_name;
            
         //   break;  
        //}
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
}    