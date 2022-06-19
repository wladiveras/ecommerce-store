<?php

namespace App\Http\Controllers;

use App\Models\Finish;
use App\Models\PricingRole;
use App\Models\PricingRules;
use App\Models\Product;
use App\Models\Sku;
use DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use marcusvbda\uploader\Controllers\UploaderController as Uploader;

class SystemColorController extends Controller
{

    private $url;
    private $tokenType;
    private $endpoint;
    private $username;
    private $password;
    private $authString;
    private $action;
    public $data;
    public $refId;
    public $response;

    public function __construct()
    {
        $this->url = config('systemcolor.url');
        $this->tokenType = config('systemcolor.tokenType');
        $this->username = config('systemcolor.username');
        $this->password = config('systemcolor.password');

    }

    public function index()
    {
        return view('pages.api.import');
    }

    private function calcMinPrice()
    {
        DB::table("min_price_product")->truncate();
        DB::table(env('ECOMMERCE_DB_DATABASE') . ".min_price_product")->truncate();
        $products = Product::get();
        $roles = PricingRole::get();

        foreach ($products as $product) {
            $row = [];
            $row = ["pricing_role_id" => null, "min_price" => $product->skus->min("price"), "product_id" => $product->id];
            DB::table("min_price_product")->insert(["pricing_role_id" => null, "min_price" => $product->skus->min("price"), "product_id" => $product->id]);

            DB::table(env('ECOMMERCE_DB_DATABASE') . ".min_price_product")->insert($row);

            foreach ($roles as $role) {
                $row["pricing_role_id"] = $role->id;
                $price = PricingRules::where("pricing_role_id", $role->id)->where("product_id", $product->id)->min("price");
                $row["min_price"] = ($price ?  floatval($price) : floatval(0));
                DB::table("min_price_product")->insert($row);
                DB::table(env('ECOMMERCE_DB_DATABASE') . ".min_price_product")->insert($row);
            }

        }
    }

    public function upload_images()
    {
        foreach (Product::get() as $product) {
            $path = public_path() . "\product_images" . "\\" . $product->slug;
            if (is_dir($path)) {
                $dir = opendir($path);
                while (false !== ($file = readdir($dir))) {
                    if (!in_array($file, [".", ".."])) {
                        // echo $path." ".$file."<br>";
                        $upload = Uploader::upload($path . "\\" . $file, uniqid(), '', 'image');
                        $product->addFile($upload, 'image');
                    }
                }
                closedir($dir);
            }
        }
    }

    public function import($action,$auth = [])
    {
        $this->action = $action;
        switch ($this->action) {

            case "products";
                $products = json_decode(config('products'));
                //$this->storeProducts($products);
                return response()->json(['success' => true, 'message' => null, 'data' => null]);
                break;

            case "pricing-rules":
                $pricingRules = json_decode(config('pricingRules'));
                //$this->storePricingRules($pricingRules);
                return response()->json(['success' => true, 'message' => null, 'data' => null]);

                break;

            case "calc-min-price":
                //$this->calcMinPrice();
                return response()->json(['success' => true, 'message' => null, 'data' => null]);

                break;

            case "finishes":
                $finishes = json_decode(config('finishes'));
                //$this->storeFinishes($finishes);
                return response()->json(['success' => true, 'message' => null, 'data' => null]);

                break;

            case "images";
                $this->upload_images();
                return response()->json(['success' => true, 'message' => null, 'data' => null]);
                break;
            case "reseller":
                $this->username = $auth['user_name'];
                $this->password = $auth['password'];
                $this->getData($this->action, $this->refId);
                $result['data'] = $this->data;
                $result['response'] = $this->response;
                return $result;
                break;
            default:
                $this->getData($this->action, $this->refId);
                $result['data'] = $this->data;
                $result['response'] = $this->response;
                return $result;

            break;

        }

    }

    private function getAuthString()
    {
        return $this->authString = base64_encode($this->username . ':' . $this->password);
    }

    private function getAction($action)
    {
        switch ($action) {
            case 'product':
                $this->endpoint = config('systemcolor.products');
                break;

            case 'reseller':
                $this->endpoint = config('systemcolor.reseller');
                break;

            case 'prices':
                $this->endpoint = config('systemcolor.pricing_rules');
                break;
        }

        return $this->endpoint;
    }

    private function getData($action, $refId = null)
    {

        $this->authString = $this->getAuthString();
        $this->endpoint = $this->getAction($action);

        $url = $this->url . $this->endpoint . $refId;
        $client = new Client();

        $headers = [
            'Authorization' => $this->tokenType . ' ' . $this->authString,
        ];

        $array['headers'] = $headers;

        try {

            $guzzleReturn = $client->request('GET', $url, $array);

            $this->data = $guzzleReturn->getBody();
            //   $this->data = htmlentities($this->data);
            //   $this->data = html_entity_decode($this->data);

            $this->data = json_decode($this->data, true);
            $this->response = $guzzleReturn->getStatusCode();

        } catch (RequestException $e) {

            $this->response = $e->getCode() . " " . $e->getMessage();
        }

        return $this;
    }

    public function storeProducts($products)
    {

        set_time_limit(9999);

        $last = "";
        $lastDepartment = "";

        $importedProducts = [];

        foreach ($products as $product) {

            if (!in_array($product->name . "_" . $product->department, $importedProducts)) {
                $next = $product->name;
                $nextDepartment = $this->getDepartment($product->department);
                if (($last != $next) || ($last == $next && $lastDepartment != $nextDepartment)) {
                    $variations = [];
                    foreach ($product->variations as $name => $row) {
                        if ($name != "Quantidade") {
                            $variations[] = trim($name);
                        }
                    }
                    if(!isset($product->sizes) && $product->showDetails == true)
                    {
                        $variations[] = "Detalhamentos";
                    }

                    $variations[] = "Quantidade";

                    $data['name'] = $product->name;
                    $data['type'] = (@$product->type) ? $product->type : json_encode(["UP", "CA"]);
                    $data['variations'] = $variations;
                    $data['status'] = 1;
                    $data['base_price'] = number_format((int) $product->price, 2, '', '.');
                    $data['description'] = "<p>" . $product->description . "<p>";
                    $data['department'] = $this->getDepartment($product->department);
                    $data['icon'] = (@$product->icon) ? $product->icon : 'adesivo.svg';
                    $product_created = Product::create($data);
                    $last = $product_created->name;
                    $lastDepartment = $product_created->department;
                    unset($variations);

                    $importedProducts[] = $product->name . "_" . $product->department;

                    $skus = array_filter($products, function ($row) use ($product) {
                        return (($row->name == $product->name) && ($row->department == $product->department));
                    });
                    foreach ($skus as $sku) {
                        $this->addSkusOnStore($product_created, $sku);
                    }
                }
            }
        }

        return $this;
    }

    private function addSkusOnStore(Product $product, $sku)
    {

        set_time_limit(9999);
        $data['product_id'] = $product->id;
        $data['ref_id'] = $sku->ref_id;
        $data['prepared_in'] = $sku->prepared_in;
        $data['weight'] = str_replace(',', '.', (isset($sku->weight)) ? $sku->weight : 0.500);
        $data['price'] = str_replace(',', '.', $sku->price);
        $data['name'] = "";
        $data['attributes'] = "";
        $data['amount_rule'] = $this->getAmountRule($sku->variations->Quantidade, $sku);
        $sizes = (isset($sku->sizes)) ? $sku->sizes : [];
        $data["custom_extra_info"] = json_encode([
            "cover" => $sku->cover,
            "sizes" => $sizes,
            "obs" => $sku->obs,

        ]);

        $data['data'] = json_encode($sku);

        foreach ($sku->variations as $key => $value) {
            // $attributes[] = $value;
            if ($key != "Quantidade") {
                $attributes[] = trim($value);
            }
        }

        if(!isset($sku->sizes) && $sku->showDetails == true)
        {
            $attributes[] = $sku->description;
        }

        $attributes[] = $sku->variations->Quantidade;
        $data['attributes'] = $attributes;

        foreach ($attributes as $value) {
            $data['name'] .= $value . " ";
        }
        Sku::create($data);
        $route = "";

        return $this;

    }

    private function getDepartment($data)
    {
        switch ($data) {
            case 'COMUNICAÇÃO VISUAL':
                $data = 1;
                break;

            case 'OFFSET':
                $data = 2;
                break;

            case 'DEPARTAMENTO DIGITAL':
                $data = 3;
                break;
        }
        return $data;

    }

    private function storePricingRules($rules)
    {
        set_time_limit(9999);
        foreach ($rules as $rule) {
            $data['pricing_role_id'] = PricingRole::where('ref_id', $rule->cod_tabela)->value('id');
            $refProductID = $rule->produto_id;
            $data['sku_id'] = $this->getSkuID($rule->produto_id);
            $data['product_id'] = $this->getProductID($data['sku_id']);
            $data['price'] = (float) $rule->valor;
            PricingRules::create($data);
        }

    }

    private function storeFinishes($finishes)
    {
        set_time_limit(9999);

        foreach ($finishes as $finish) {
            $prices = $finish->categoria;
            $formatedPrices = [];
            foreach($prices as $refId => $price) {
                $id = $this->getPricingRoleID($refId);
                $formatedPrices[$id] = $price;
            }
            $data['name'] = $finish->descricao;
            $data['sku_id'] = $this->getSkuID($finish->produto_id);
            $data['finish_ref_id'] = $finish->acabamento_id;
            $data['prices'] = $formatedPrices;
            $data['prepared_in'] = (float) $finish->prazo_cra;
            Finish::create($data);
        }
        return $this;

    }

    private function getSkuID($refID)
    {
        $id = Sku::where('ref_id', $refID)->value('id');
        $id = ($id != null) ? $id : 33333;
        return $id;
    }

    private function getProductID($skuID)
    {
        $id = Sku::where('id', $skuID)->value('product_id');
        $id = ($id != null) ? $id : 33333;
        return $id;
    }

    private function getPricingRoleID($refRoleID)
    {
        $roleID = PricingRole::where('ref_id', $refRoleID)->value('id');
        return $roleID;
    }

    private function getAmountRule($amount, $sku = null)
    {
        $amountRule = "";
        switch ($amount) {
            case strpos($amount, 'partir') > 0:
                preg_match_all('!\d+!', $amount, $result);
                $amountRule = ">" . $result[0][0];
                break;

            case strpos($amount, 'até') > 0:
                preg_match_all('!\d+!', $amount, $result);
                $amountRule = "<" . $result[0][1];
                break;

            default:
                $amountRule = $amount;
                break;
        }

        return $amountRule;

    }
}
