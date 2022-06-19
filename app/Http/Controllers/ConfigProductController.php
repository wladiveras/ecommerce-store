<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\BusinessRule\ConfigProduct as RuleConfigProduct;

class ConfigProductController extends Controller
{
    private $qty_index = ["qty", "quantidade", "quant", "qtde", "qtd", "qnt"];
    private $replace_in_json = "\\\\";
    private $variations = [];

    private function remove_index($keys, $values)
    {
        $_keys = array_map('strtolower', $keys);
        foreach ($values as $value) {
            $index = array_search("quantidade", $_keys);
            if ($index) {
                unset($keys[$index]);
            }
        }
        return $keys;
    }

    private function has_qty($keys, $values, $skus, $process = false)
    {
        $_keys = array_map('strtolower', $keys);
        foreach ($values as $value) {
            $index = array_search($value, $_keys);
            if (@$index >= 0) {
                $attributes = [];
                if ($process) {
                    $attributes = $this->qty_process($keys, $index, $skus);
                }
                return ["has_qty" => true, "attributes" => $attributes];
            }
        }
        return ["has_qty" => false, "attributes" => null];
    }

    private function qty_process($keys, $step, $skus)
    {
        $options = [];
        $key = false;
        if (isset($keys[$step])) {
            $key = $keys[$step];
            foreach ($skus as $sku) {
                $sku->load("finishes");
                $attributes = $sku->attributes;
                if (!in_array($attributes[$step], $options, true)) {
                    $value = ["rule" => $sku->amount_rule, "amount" => $attributes[array_search("Quantidade", $this->variations)], "sku" => $sku->toArray(), "price" => $sku->reseller_price];
                    array_push($options, $value);
                }
            }
        }
        return [$options, $key];
    }

    private function option_process($keys, $step, $skus)
    {
        $options = [];
        $key = false;
        if (isset($keys[$step])) {
            $key = $keys[$step];
            foreach ($skus as $sku) {
                $attributes = $sku->attributes;
                if (!in_array($attributes[$step], $options, true)) {
                    $options[] = $attributes[$step];
                }
            }
        }
        $details = $this->get_attribute_details($skus->pluck("id")->toArray(), $options, $step);
        return [$options, $key, $details];
    }

    private function get_attribute_details($sku_ids, $attributes, $step)
    {
        $details = [];
        $skus = Sku::whereIn("id", $sku_ids);
        foreach ($attributes as $attr) {
            $sku = clone $skus;
            $sku = $this->getDetailSku($sku, $attr, $step);
            $details[] = @$sku->variation_detail[$step] ? @$sku->variation_detail[$step] : ["image" => null, "description" => null];
        }
        return $details;
    }

    private function getDetailSku($skus, $attr, $step)
    {
        $skus = $skus->whereJsonContains("attributes", $attr)->get();
        foreach ($skus as $sku) {
            $attributes = $sku->attributes;
            if ($attributes[$step] == $attr) return $sku;
        }
        return null;
    }

    private function process_attr($variations, $skus, $step)
    {
        $keys = array_keys($variations);
        $extra["qty"] = $this->has_qty($keys, $this->qty_index, $skus);
        $keys = $this->remove_index($keys, $this->qty_index);
        $result = $this->option_process($keys, $step, $skus);
        return ["options" => $result[0], "details" => $result[2], "key" => $result[1], "skus" => $skus, "extra" => $extra];
    }

    private function process_variations($variations)
    {
        $result = [];
        foreach ($variations as $variation) {
            $result[$variation] = [];
        }
        return $result;
    }

    private function get_options($product, $step = 1, $selected_options)
    {
        $variations = $product->variations;
        $variations = $this->process_variations($variations);
        //$skus = $product->skus;
        $skus = $product->skus->where("status", true);
        if (count($selected_options) > 0) {
            //Log::debug('chegou');
            $skus = $this->filter_sku($product, $selected_options, $step);
        }

        $options = $this->process_attr($variations, $skus, $step);

        return $options;
    }

    private function filter_sku($product, $options)
    {

        for ($i = 0; $i < count($options); $i++) {
            if ($options[$i] == null) {
                $options[$i] = "";
            }
        }

        $json = json_encode($options);
        $json = substr($json, 0, strlen($json) - 1);
        $json = preg_replace("^{$this->replace_in_json}^", '', $json);
        $query = "REPLACE(attributes,'{$this->replace_in_json}','') like '%$json%'";
        //$query = Sku::where("product_id", $product->id)->whereRaw($query);
        $query = Sku::where("product_id", $product->id)->whereRaw($query)->where('status', true);
        //  dd($query->toSql());       
        $skus = $query->get();
        return $skus;
    }

    private function get_qty($product, $selected_options)
    {
        $variations = $product->variations;
        $this->variations = $variations;
        $variations = $this->process_variations($variations);
        $keys = array_keys($variations);
        $skus = $product->skus;
        if (count($selected_options) > 0) {
            $skus = $this->filter_sku($product, $selected_options);
        }
        $qty = $this->has_qty($keys, $this->qty_index, $skus, true);
        return $qty;
    }

    private function get_final_sku($product, $selected_options)
    {
        $variations = $product->variations;
        $variations = $this->process_variations($variations);
        $skus = $this->filter_sku($product, $selected_options);
        return $skus->first()->load("finishes");
    }

    public function getConfigSkuFinal(Request $request)
    {
        try {
            $data = $request->all();
            $product = Product::findOrFail($data["product_id"]);
            $variations = $product->variations;
            $skus = [];

            foreach ($data["qtys"] as $qty) {
                $options = $data["selected_options"];
                if (in_array("Quantidade", $variations)) {
                    $options[] = (string) $qty["value"];
                }
                $sku = $this->get_final_sku($product, $options);
                $skus[] = ["sku" => $sku, "qty" => $qty["qty"], "attribute" => $sku->attributes[count($sku->attributes) - 1]];
            }
            return response()->json(['success' => true, 'message' => null, 'data' => $skus]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function getConfigOptions(Request $request)
    {
        try {
            $data = $request->all();
            $step = $data["option_step"];
            $product = Product::findOrFail($data["product_id"]);
            $total_steps = count($product->variations);
            $messages = [];
            $options = $this->get_options($product, $step, $data["selected_options"]);

            if ($total_steps - 1 == $step) {
                foreach ($options["skus"] as $sku) {
                    $info = !is_array($sku->custom_extra_info) ? json_decode($sku->custom_extra_info) : $sku->custom_extra_info;
                    if (@$info->obs) {
                        $messages[] = $info->obs;
                        break;
                    }
                }
            }


            $result = [
                "extra"       => $options["extra"],
                "skus"        => (count($options["skus"]) > 1 ?  count($options["skus"]) : $options["skus"]),
                "sku_ids"     => (($total_steps - 1 == $step) ? $options["skus"]->pluck("id") : []),
                "final"       => (count($options["skus"]) > 1 ? false : true),
                "options"     => $options["options"],
                "details"     => $options["details"],
                "key"         => $options["key"],
                "total_steps" => $total_steps,
                "messages"    => $messages
            ];

            return response()->json(['success' => true, 'message' => null, 'data' => $result]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function getConfigQty(Request $request)
    {
        try {
            $data = $request->all();

            $product = Product::findOrFail($data["product_id"]);
            // $total_steps = count($product->variations);
            $result = $this->get_qty($product, $data["selected_options"]);
            // $attr = $result["attributes"][0];
            return response()->json(['success' => true, 'message' => null, 'data' => $result["attributes"]]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function get_calc_qty_tshirts(Request $request)
    {
        try {
            $data = $request->all();
            $skus = Sku::whereIn("id", $data["ids"])->get();
            $_sizes = [];
            foreach ($skus as $sku) {
                $extra = json_decode($sku->custom_extra_info);
                $sizes = (array) $extra->sizes;
                foreach ($sizes as $key => $value) {
                    if (!isset($_sizes[$key])) {
                        $_sizes[$key] = $value;
                    }
                }
            }
            return response()->json(['success' => true, 'message' => null, 'data' => $_sizes]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    private function get_skus_qty_tshirts($data, $qty)
    {
        $skus = Sku::whereIn("id", $data["ids"])->orderBy("amount_rule", "asc")->get();
        $qty  = $data["qty"];
        $_skus = [];
        foreach ($skus as $sku) {
            $value = (int) preg_replace('/[^0-9]/', '', $sku->amount_rule);
            $operator = preg_replace('/\d/', '', $sku->amount_rule);
            if ($operator != "") {
                if (eval("return ({$qty}{$operator}={$value});")) {
                    $_skus[] = ["qty" => $qty, "sku" => $sku, "value" => $sku->amount_rule];
                    break;
                }
            } else {
                $_skus[] = ["qty" => $qty, "sku" => $sku, "value" => $sku->amount_rule];
                break;
            }
        }
        if (count($_skus) <= 0) {
            return  "A partir de " . preg_replace('/[^0-9]/', '', $sku->amount_rule);
        }
        return $_skus;
    }

    public function calc_qty_tshirts(Request $request)
    {
        try {
            $data = $request->all();
            $result = $this->get_skus_qty_tshirts($data, $data["qty"]);
            if (is_array($result)) {
                return response()->json(['success' => true, 'message' => null, 'data' => $result]);
            }
            return response()->json(['success' => false, 'message' => "A quantidade deste produto deve ser " . $result, 'data' => null]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }


    public function calc_qty(Request $request)
    {
        try {
            $data         =  $request->all();
            $amount       =  $data["default_amount"];
            $options      =  $data["options"];
            $rule         =  new RuleConfigProduct();
            $combinations =  $rule->getOrderMinPriceCombination($options, $amount);
            return response()->json(['success' => true, 'message' => null, 'data' => $combinations]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function get_calc_finishes(Request $request)
    {
        try {
            $data = $request->all();
            $controller    =  new FinishesController();
            $finishREF     =  $data["finish_ref_id"];
            $department    =  $data["department"];
            $amount        =  $data["qty_sku"];
            $countFinishes =  $data["qty_finish"];
            $skuID         =  $data["sku_id"];
            $result        =  $controller->calcFinishes($skuID, $finishREF, $department, $amount, $countFinishes);
            return response()->json(['success' => true, 'message' => null, 'data' => $result]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function check_has_options(Request $request)
    {
        try {
            $data = $request->all();
            $product = Product::find($data["product_id"]);
            $variations = $product->variations;
            if (count($variations) > 1) {
                return response()->json(['success' => true, 'message' => null, 'data' => null]);
            }
            if ((count($variations) == 1) && (in_array("Qunatidade", $variations))) {
                $skus = $product->skus;
                if ($skus->count() > 1) {
                    return response()->json(['success' => true, 'message' => null, 'data' => null]);
                }
            }
            return response()->json(['success' => false, 'message' => null, 'data' => null]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }
}
