<?php

namespace App\Http\Controllers;

use App\Models\AdditionalConfig;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sku;
use Config;

class PricingTableController extends Controller
{
    public function getData($product, $item, Request $request)
    {
        switch ($item) {
            case "fields":
                $product = Product::findOrfail($request["product_id"]);
                $product->setAppends([]);
                $product->templates = $product->files(['pdf', 'ai', 'psd', 'cdr'])->get()->toArray();
                return ["fields" => $this->getFieldsFilterProduct($product), "product" => $product];
                break;
            case "skus":
                $filter = $request->all();
                $page = $filter["page"];
                $product = Product::findOrFail($filter["product_id"]);
                $skus = $product->skus()->where("status", true);
                $skus = $this->filterByAttribute($filter, $product, $skus, "Quantidade");
                $skus = $this->filterByAttribute($filter, $product, $skus, "Acabamento Padrão");
                $skus = $this->filterByAttribute($filter, $product, $skus, "Mídia");
                $skus = $this->filterByAttribute($filter, $product, $skus, "Cor");
                $skus = $this->filterSize($filter, $product, $skus);
                $skus = $skus->orderBy("price", "asc")->paginate(20);
                return $skus;
                break;
            default:
                return $request->all();
                break;
        }
    }

    private function getFieldsFilterProduct($product)
    {
        $fields = $this->getVariationAttributes($product, ['Acabamento Padrão', 'Mídia', 'Cor', 'Quantidade']);
        $sizes = $this->getSizes($product);
        if (count($sizes) > 0) $fields["Tamanho"] = $sizes;
        return $fields;
    }

    private function getSizes($product)
    {
        $results = [];
        foreach ($product->skus as $sku) {
            $data = is_string($sku->data) ? json_decode($sku->data) : [];
            $sizes = @$data->sizes ? $data->sizes : [];
            foreach ($sizes as $size) {
                $results[] = $size;
            }
        }
        return array_unique($results);
    }

    private function getVariationAttributes($product, $variations)
    {
        $positions = [];
        foreach ($variations as $v) {
            $pos = array_search($v, $product->variations);
            if ($pos) $positions[$v] = $pos;
        }
        $result = [];
        foreach ($product->skus()->where("status", true)->get() as $sku) {
            foreach ($positions as $key => $value) {
                if (!@$result[$key])  $result[$key] = [];
                if (!in_array($sku->attributes[$value], $result[$key])) {
                    if ($key != "Quantidade") $result[$key][] = $sku->attributes[$value];
                    else {
                        $_value = ($sku->attributes[$value] == "1") ? "Qtde Livre" : $sku->attributes[$value];
                        if (!in_array($_value, $result[$key])) $result[$key][] = $_value;
                    }
                }
            }
        }
        return $result;
    }

    private function filterSize($filter, $product, $skus)
    {
        if (@$filter["filter_size"]) {
            $ids = [];
            if (!$filter["filter_size"] == "Sem Tamanho") {
                foreach ($skus->get() as $sku) {
                    $data = json_decode($sku->data);
                    $sizes = (array) (@$data->sizes ? $data->sizes : []);
                    if (in_array($filter["filter_size"], $sizes)) $ids[] = $sku->id;
                }
                $skus = $skus->whereIn("id", $ids);
            }
        }
        return $skus;
    }

    private function filterByAttribute($filter, $product, $skus, $index_variation)
    {
        if (@$filter[$index_variation]) {
            $position = array_search($index_variation, $product->variations);
            $ids = [];
            $value = ($filter[$index_variation] == "Sem " . $index_variation) ? "" : $filter[$index_variation];
            $value = $value == "Qtde Livre" ? "1" : $value;
            foreach ($skus->get() as $sku) {
                if ($sku->attributes[$position] == $value) $ids[] = $sku->id;
            }
            $skus = $skus->whereIn("id", $ids);
        }
        return $skus;
    }

    public function loadSetup(Product $product, Sku $sku, $settings, Request $request)
    {
        $settings = @json_decode(base64_decode($settings));
        if (!$settings) abort(404);
        if (!$product) return abort(404);
        $skus = collect([$sku]);
        $options = $skus->first()->attributes_list();
        if (isset($options["Quantidade"])) {
            $hasQty = true;
            $amount = $options["Quantidade"];
            unset($options["Quantidade"]);
        }

        $configs = AdditionalConfig::with('attr')->get()->toArray();
        $route_w2p = Config::get("web2print.route");

        if (Config::get("web2print.enabled")) {
            $route_w2p = Config::get("web2print.route");
            $verified_w2p =  $this->verifyWeb2Print($product->id, $route_w2p);

            if (!$verified_w2p["success"]) {
                for ($i = 0; $i < count($configs); $i++) {
                    if ($configs[$i]["type"] == "W2P") {
                        unset($configs[$i]);
                        break;
                    }
                }
            }
        }
        $data = [
            'product' => $product,
            'files' => $product->files(['pdf', 'ai', 'psd', 'cdr'])->get(),
            'configs' => $configs,
            'clone'  => $request->all(),
            'is_preset' => true,
            'qty' => $settings->calc_qty,
            'skus' => $skus,
            'has_qty' => @$hasQty,
            'sku_amount' => $amount,
            'options' => $options,
            'art_type' => $settings->art,
            'needs_measures' => $skus->first()->has_measures(),
        ];
        if (@$settings->measures) {
            $data["measures"] = [
                "unit"   => "m",
                "width"  => $settings->measures->width,
                "height" => $settings->measures->height,
            ];
        }
        if (@$settings->sizes) {
            $data["sizes"] = [[
                "index"  => array_search($settings->sizes, $sku->custom_extra_info["sizes"]),
                "name"   => $settings->sizes,
                "qty"    => $settings->qty
            ]];
        }
        $product->load("sizes");
        $w2p_token = @$verified_w2p["success"] ? $verified_w2p["token"] : "";

        $route_upload = route('checkout.upload.file', ['product' => $product->slug]);
        return view('pages.checkout.upload', compact('data', 'route_upload', 'w2p_token', 'route_w2p'));
    }

    public function fastbuy_show(Product $product)
    {
        return view('pages.princing_table.index', compact("product"));
    }

    public function fastbuy(Product $product, Request $request)
    {
        $data = $this->validadeFastBuy($request);
        $sku = $data->sku;
        unset($data->sku);
        $hash = base64_encode(json_encode($data));
        $route = route('product.load-sku', [
            "product" => $product->slug,
            "sku" => $sku->slug,
            "settings" => $hash
        ]);
        return redirect($route);
    }

    private function validadeFastBuy($request)
    {
        $data = $request->all();
        $sku  =  Sku::findOrFail($data["sku_id"]);
        $_data = ["sku" => $sku];
        if (!@$data["qty"]) abort(404);
        $_data["qty"] = (int) $data["qty"];
        if (!@$data["calc_qty"]) abort(404);
        $_data["calc_qty"] = (int) $data["calc_qty"];
        $_data = $this->validadeFastBuySize($sku, $data, $_data);
        $_data = $this->validadeFastBuyMeasures($sku, $data, $_data);
        if (@$data["art"]) {
            if (!in_array($data["art"], ["CA", "UP"])) abort(404);
            $_data["art"] = $data["art"];
        }
        return (object) $_data;
    }

    private function validadeFastBuySize($sku, $data, $_data)
    {
        if (is_array(@$sku->custom_extra_info["sizes"])) {
            if (count($sku->custom_extra_info["sizes"]) > 0) {
                if (!@$data["size"]) abort(404);
                if (!array_search($data["size"], $sku->custom_extra_info["sizes"])) abort(404);
                $_data["sizes"] = $data["size"];
                return $_data;
            }
        }
        return $_data;
    }

    private function validadeFastBuyMeasures($sku, $data, $_data)
    {
        $index = array_search("Formato", $sku->product->variations);
        $attribute = $sku->attributes[$index];
        $hasMeasure = (strpos($attribute, "M²") !== false);
        if ($hasMeasure) {
            $attribute = str_replace("M² ", "", $attribute);
            $attribute = str_replace("[", "", $attribute);
            $attribute = str_replace("]", "", $attribute);
            $attribute = explode(",", $attribute);
            $attribute = array_map(function ($x) {
                if ($x == "-") return null;
                else return (int) $x / 100;
            }, $attribute);
            $measures = @json_decode($data["measures"]);
            if (!@$measures->height) abort(404);
            if (!@$measures->width) abort(404);
            if (($attribute[0]) && ($measures->height > $attribute[0])) abort(404);
            if (($attribute[1]) && ($measures->width > $attribute[1])) abort(404);
            $_data["measures"] = $measures;
            return $_data;
        }
        return $_data;
    }

    private function verifyWeb2Print($produtId, $route_w2p)
    {
        $method =  "POST";
        $endpoint = $route_w2p . "/api/verify";
        $token = Config::get("web2print.token");
        $params["product_id"] = $produtId;
        $params["token"] = $token;

        $data = [
            "json"    => $params,
            "headers" => ["origin" => $_SERVER["HTTP_HOST"]]
        ];

        $client = new Client();
        $guzzleReturn = $client->request($method, $endpoint, $data);
        $result = $guzzleReturn->getBody();
        $result = json_decode($result, true);
        return  $result;
    }
}
