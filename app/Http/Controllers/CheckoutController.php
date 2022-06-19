<?php

namespace App\Http\Controllers;

use App;
use App\Http\Requests\CreateImage;
use App\Models\AdditionalConfig;
use App\Models\AdditionalConfigAttr;
use App\Models\Product;
use App\Models\Sku;
use App\Models\Finish;
use App\Services\AlertService;
use Auth;
use DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use marcusvbda\uploader\Controllers\UploaderController as Uploader;
use App\Models\File as _File;
use App\Http\Resources\CartResource;
use App\Http\Requests\RequestConfigProduct;
use App\Models\UserCard;
use ismaelgr\getnet\services\Payment;
use App\Services\Payment\CieloPayment;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Config;



class CheckoutController extends Controller
{
    public function index()
    {
        $this->flashIfFactoryClosing();
        $user_model = Auth::user();
        $user = $user_model->toArray();
        $cartController = App::make(CartController::class);
        $cart = $cartController->init($user['code']);
        $content = CartResource::collection($cart->getContent());
        session(['fingerprint' => md5(session('_token') . $user_model->id)]);
        $cards = UserCard::where("user_id", Auth::user()->id)->get();
        return view('pages.checkout.form', compact('cart', 'user', 'content', 'cards'));
    }

    public function getFinishedOrderMessage($type)
    {
        switch ($type) {
            case "bankslip":
                return 'Pedido realizado, porém o pagamento do boleto ainda está pendente, aguardando pagamento ...';
                //case "paylater":
                //    return "O pedido será processado em breve";
            default:
                return "Transação finalizada com sucesso.";
        }
    }

    public function getcard(Request $request)
    {
        $paymentService = config('payment.payment_service');
        switch ($paymentService) {
            case 'getnet':
                $payment = new Payment();
                $result = $payment->makePayment($request);
                return response()->json(['success' => true, 'message' => null, 'data' => $result]);
                break;

            case 'cielo':
                $cieloPayment = new CieloPayment();
                $result = $cieloPayment->getCreditCardSaved($request);
                return response()->json(['success' => true, 'message' => null, 'data' => $result]);
                break;
            default:
                return response()->json(['success' => false, 'message' => "Serviço de Pagamento Inválido", 'data' => null]);
                break;
        }
    }

    public function finish()
    {
        $data = [];
        $user = \Auth::user();
        $order = \App\Models\Order::latest()->first();

        $data['user'] = $user;
        $data['order'] = $order;
        $data['type'] = $order->data['payment']['type'];
        $data['message'] = $this->getFinishedOrderMessage($data['type']);
        //dd($order);
        return view('pages.checkout.complete', $data);
    }

    public function flashIfFactoryClosing()
    {
        if (isFactoryClosed() || isFactoryClosing()) {
            AlertService::flash('warning', "Atenção, pedidos realizados após as 17 horas terão acréscimo de 1 dia útil automaticamente.");
        }
    }

    public $shipping_types = [
        'rota' => ['payment' => ['name' => 'Rota', 'price' => '', 'rawPrice' => 0, 'rawTime' => 0], 'shipping' => ['name' => 'Frete', 'option' => 'normal', 'price' => '', 'rawPrice' => 0, 'rawTime' => 0, 'time' => "Calcule o frete"]],
        'frete' => ['shipping' => ['name' => 'Frete', 'price' => '', 'rawPrice' => 0, 'rawTime' => 0], 'client_shipping' => ['name' => 'Enviar para o Cliente (frete)', 'option' => 'client', 'price' => '', 'rawPrice' => 0, 'rawTime' => 0, 'time' => "Calcule o frete"]],
    ];

    public function generateEstimatedTimeText($types, $time, $extra_time)
    {
        foreach ($types as &$type) {
            if ($type['name'] == "Rota") {
                $type['name'] = "Entrega pela " . \Auth::user()->reseller->route->name;
            }
            $time = $type['rawTime'] = $time + $type['rawTime'];
            $time += $extra_time;
            if (!isset($type['time'])) {
                $type['time'] = "previsão de entrega $time " . str_plural('dia', $time) . (($time > 1) ? ' úteis' : ' útil');
            }
        }
        return $types;
    }

    public function availableShippingTypes($user)
    {
        $user = \Auth::user();
        $cartController = \App::make(CartController::class);
        $cart = $cartController->init($user->code);

        $exclusive = $this->shipping_types[$user->type];
        $shipping_types = [
            'withdrawal' => [
                'name' => 'Retirar em uma loja Cria Fácil',
                'price' => '',
                'rawPrice' => 0,
                'rawTime' => 0
            ],
            'retirada_balcao' => [
                'name' => 'Retirar em um balcão de entrega',
                'price' => '',
                'rawPrice' => 0,
                'rawTime' => 0
            ]
        ];

        $route = $user->reseller->route;
        if (@$exclusive['payment'] && $route) {
            $exclusive['payment']['rawTime'] += @$route['info']['shipping_delay'];
        }

        $closed_factory = isFactoryClosed();

        $sku_time = $cartController->longest_sku_preparation_time($cart);
        $shipping_types = array_merge($shipping_types, $exclusive);
        $shipping_types = $this->generateEstimatedTimeText($shipping_types, $sku_time, $closed_factory);
        session(["shipping_data" => $shipping_types, ['delay' => $closed_factory]]);
        return $shipping_types;
    }

    public function availablePaymentMethods($user)
    {
        $methods = [
            'creditcard',
            'bankslip'
        ];

        if ($user->reseller->can_be_trusted) {
            $methods[] = 'paylater';
        }

        return $methods;
    }

    public function loadCheckoutData()
    {
        $user = \Auth::user();
        $res = [
            'payment' => $this->availablePaymentMethods($user),
            'shipping' => $this->availableShippingTypes($user),
        ];


        return [
            'success' => true,
            'data' => $res,
        ];
    }

    public function upload($product, $image = null, Request $request)
    {
        //    
        if (!$product) return abort(404);
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
        // 
        $data = [
            'product' => $product,
            'files' => $product->files(['pdf', 'ai', 'psd', 'cdr'])->get(),
            'configs' => $configs,
            'clone'  => $request->all()
        ];

        $product->load("sizes");
        $route_upload = route('checkout.upload.file', ['product' => $product->slug]);
        $w2p_token = @$verified_w2p["success"] ? $verified_w2p["token"] : "";
        return view('pages.checkout.upload', compact('data', 'route_upload', 'w2p_token', 'route_w2p'));
    }

    public function uploadFile(CreateImage $request)
    {
        // echo " ";ob_flush();flush();
        $data = $request->all();
        Log::debug('upload file->  '.json_encode($data) );
        $file = Uploader::upload($data['file'], $data['file']->getClientOriginalName(), @$data['_alt'], null, \Auth::user()->id);
        if (isset($data['private'])) {
            if ($data['private'] == 1) {
                $file->setPrivate();
            } else {
                $file->setPublic();
            }
        }

        return response()->json(['success' => true, 'message' => null, 'data' => $file]);
    }

    public function removeUploadedFile(Request $request)
    {
        $data = $request->all();
        _File::where("id", $data["id"])->delete();
        return response()->json(['success' => true, 'message' => null, 'data' => null]);
    }

    private function mapVariations($variations, $base)
    {
        $res = [];
        $quantity = '';
        foreach ($variations as $key => $variation) {
            if ($key == 'qty') {
                $quantity = $variation;
                continue;
            }
            $res[$key] = $variation;
        }
        if ($quantity != '') {
            $res['Quantidade'] = $quantity;
        }
        $res = array_merge($base, $res);

        return array_values($res);
    }
    private function processAmounts($skus)
    {
        $hasRule = false;
        $data = [];
        foreach ($skus as $i => $sku) {
            $attrs = $sku->attributes_list();
            if (preg_match("([^A-z0-9])", $sku->amount_rule)) {
                $hasRule = true;
            }
            $data[$sku->id]['data'] = $attrs;
            $data[$sku->id]['model'] = $sku;
            $data[$sku->id]['price'] = $sku->price;
            if ($hasRule) {
                $data[$sku->id]['data']["Quantidade"] = preg_replace("/[^0-9]/", "", $sku->amount_rule);
            }
        }
        $data = collect($data)->sortByDesc('data.Quantidade');
        return [
            'skus' => $data,
            'hasRule' => $hasRule,
        ];
    }
    public function findSkus($options, $excluded = 'qty', $request, $data)
    {
        if (isset($options[$excluded])) {
            $product = Product::where(['slug' => $request->route('product')])->FirstOrFail();
            $variations = $this->mapVariations($data['options'], array_flip($product->variations));
            unset($variations[count($variations) - 1]); //removendo Quantidade
            $slug = strtolower(normalize(implode('-', $variations)));
        } else {
            $slug = strtolower(normalize(implode('-', $options)));
        }
        $slug = preg_replace("([^A-z0-9])", '-', $slug);
        $slug = preg_replace("(-----|----|---|--)", '-', $slug);
        $skus = Sku::where('slug', 'LIKE', "%$slug%")->where('product_id', $data['product_id'])->get();
        $results = $this->processAmounts($skus);
        return $results;
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

    private function getCaPrice($product, $price)
    {
        if ($product->custom_extra_info)
            if ($product->custom_extra_info["additional_custom_price"])
                if ($product->custom_extra_info["additional_custom_price"]["CA"]) $price = floatval($product->custom_extra_info["additional_custom_price"]["CA"]);
        return $price;
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
            $result =  $this->calculateFinishPrice($request, $finish, $sku_id, $order_qty, $sku_qty);
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

    private function add_skus_to_cart($data)
    {
        $product = Product::findOrFail($data["product_id"]);
        $additional = $this->get_additional_price(@array_filter($data["additional"], 'strlen'), $product);
        $_sku = ["price" => 0, "id" => null];
        foreach ($data["skus"] as $_row) {
            if ($_row["sku"]["reseller_price"] > $_sku["price"]) {
                $_sku = [
                    "id" => $_row["sku"]["id"],
                    "sku_qty" => $_row["qty"],
                    "price" => $_row["sku"]["reseller_price"],
                ];
            }
        }

        $finishes = $this->get_finishes_price($_sku["id"], $data["finishes"], $data["requests"], $data["order_qty"], $_sku["sku_qty"]);
        $cart_id = uniqid();
        $data["finishes"] = $finishes;
        $data["additional"] = $additional;
        $skus = $data["skus"];
        $order = [];
        unset($data["skus"], $data["product_id"]);
        $user = Auth::user()->toArray();
        $cartController = App::make(CartController::class);
        $cart = $cartController->init($user['code']);
        foreach ($skus as $row) {
            $sku = Sku::find($row["sku"]['id']);
            $aux = [
                "sku"        =>   $sku->toArray(),
                "upload"     =>   $data["upload"],
                "quantity"   =>   $row["qty"],
                "price"      =>   (float) $sku->reseller_price,
            ];
            $items[] = $aux;
        }

        $finishes = ($finishes ? $finishes : null);
        $additional = ($additional ? $additional : null);
        $total = $this->get_order_price($skus, $finishes, $additional, $data["measures"]);
        $order["order"] = [
            "total" => $total,
            "qty"   => (float) $data["order_qty"],
            "data"  => $data,
            "date"  => date("Y-m-d"),
            "time"  => date("H:i:s"),
            "additional" => $additional,
            "finishes" => $finishes,
            "items" => $items
        ];
        $cart_item = $cartController->sku_to_cart($order, $cart_id);
        $cart->add($cart_item);
    }

    private function get_order_price($skus, $finishes, $additional, $measures = null)
    {
        $price = 0;
        foreach ($skus as $sku) {
            $qty = (float) $sku["qty"];
            if (@$measures["height"]) {

                $height = floatval($measures["height"]);
                $width = floatval($measures["width"]);
                if (@$measures["unit"] == "cm") {
                    $height = $height / 100;
                    $width = $width / 100;
                }
                $area = $height * $width;
                $aux = $area * $qty;
                if ($aux > 1) {
                    $price += ((float) $sku["sku"]["reseller_price"]) * $aux;
                } else {
                    $price += ((float) $sku["sku"]["reseller_price"]);
                }
            } else {
                $price += ((float) $sku["sku"]["reseller_price"]) * $qty;
            }
        }
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

    public function confirmArt(RequestConfigProduct $request)
    {
        try {
            $data = $request->all();
            if ($data["chosen"] == "W2P") {
                $data = $this->processW2PartFile($data);
            }
            if (@$data["route_back"] != "clone") {
                AlertService::flash('success', "Pedido <b>{$data['ref_name']}</b> adicionado ao carrinho com sucesso, acesse o carrinho para visualizar !!");
                //Log::debug('erro login->  '.$data );
                Log::debug('business fim->  '.json_encode($data) );
            }
            $this->add_skus_to_cart($data);
            return response()->json(['success' => true, 'message' => null, 'data' => ["cart" => route('cart.index'), "products" => route('products.index')]]);
        } catch (\Exception $e) {
            @DB::rollBack();
            
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    private function processW2PartFile($data)
    {
        for ($i = 0; $i < count($data["upload"]["file"]); $i++) {
            $data["upload"]["file"][$i]["file"]["name"] = uniqid();
            $data["upload"]["file"][$i]["file"]["ref"] = uniqid();
            $data["upload"]["file"][$i]["file"]["dir"] = array_last(explode("/", $data["upload"]["file"][$i]["file"]["raw_url"]));
            $data["upload"]["file"][$i]["file"]["extension"] = array_last(explode(".", $data["upload"]["file"][$i]["file"]["raw_url"]));
            $data["upload"]["file"][$i]["file"]["type"] = "image";
            $data["upload"]["file"][$i]["file"]["raw_thumbnail"] = $data["upload"]["file"][$i]["file"]["raw_url"];
            $data["upload"]["file"][$i]["file"]["thumbnail"] = $data["upload"]["file"][$i]["file"]["raw_url"];
            $data["upload"]["file"][$i]["file"]["raw_dir"] = $data["upload"]["file"][$i]["file"]["raw_url"];
        }
        return $data;
    }

    public function update_reseller_address(Request $request)
    {
        try {
            $data = $request->all();
            $reseller = Auth::user()->reseller;
            $address = $reseller->full_address;
            $address["number"] = $data["number"];
            $address["complement"] = $data["complement"];
            $reseller->full_address = $address;
            $reseller->save();
            return response()->json(['success' => true, 'message' => null, 'data' => null]);
        } catch (\Exception $e) {
            @DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
        }
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
