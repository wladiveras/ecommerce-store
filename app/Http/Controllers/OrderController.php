<?php

namespace App\Http\Controllers;

use App;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderSku;
use App\Models\OrderShipping;
use App\Models\StatusOrder;
use App\Models\StatusOrderItem;
use App\Models\StatusOrderPayment;
use App\Models\Sku;
use App\Models\SupplyStore;
use App\Models\UserAddresses;
use App\Services\AlertService;
use Auth;
use DB;
use Illuminate\Http\Request;
use ismaelgr\getnet\GetnetClass;
use ismaelgr\jadlog\JadlogClass;
use GuzzleHttp\Client;
use App\Http\Resources\CartResource;
use Datatable\Datatable;
use Carbon\Carbon;
use marcusvbda\uploader\Controllers\UploaderController as Uploader;
use App\Services\BusinessRule\OrderTimeline;
use Illuminate\Support\Facades\Log;
// use App\Http\Controllers\Cielo\CreditCardController;
// use App\Http\Controllers\Cielo\BankSlipController;

use App\Services\Payment\PaymentService;

class OrderController extends Controller
{
    /**
     * integer used in updateShippingTime
     */
    private $shippingDelay = 0;


    public function resendArt($hashid, $pedido_id)
    {
        $data = $this->getOrderResend($hashid, $pedido_id);
        return view('pages.orders.resend', $data);
    }

    public function resendArtUpload($hashid, $pedido_id, Request $request)
    {
        $requst = $request->all();
        $this->getOrderResend($hashid, $pedido_id);
        $file = Uploader::upload($requst['file'], $requst['file']->getClientOriginalName(), @$requst['_alt'], null, Auth::user()->id);
        $file->setPublic();
        return response()->json(['success' => true, 'message' => null, 'data' => $file]);
    }

    public function resendArtSubmit($hashid, $pedido_id, Request $request)
    {
        $arts = $request->all();
        $data = $this->getOrderResend($hashid, $pedido_id);
        $dash_request = new DashboardApi();
        $dash_request->make("resend_art", ["id" => $data["pedido"]->id, "arts" => $arts]);
        $dash_request->send();
        $response = $dash_request->getResponse();
        if ($response["success"]) {
            $response["data"]["route"] = route('order.view.detail', ['hashid' => $data['order']->hashid]);
            AlertService::flash('success', $response["message"]);
            return $response;
        }
        $response["data"]["route"] = route('order.view.resend', ['hashid' => $data['order']->hashid, "pedido_id" => $data["pedido"]->id]);
        AlertService::flash('danger', $response["message"]);
        return $response;
    }

    private function getOrderResend($hashid, $pedido_id)
    {
        $status = StatusOrderItem::where("value", "with problems")->first();
        $order_id = \Hashids::decode($hashid);
        $order = Order::findOrFail($order_id)->first();
        $pedido = $order->skus()->where("id", $pedido_id)->where("status_id", $status->id)->firstOrFail();
        return ["order" => $order, "pedido" => $pedido];
    }

    public function OrdersDetails($hashid)
    {
        $id = \Hashids::decode($hashid);
        $id = (($id != []) ? $id : -1);
        $order = Order::where('id', $id);
        if ($order->count() <= 0) {
            abort(404);
        }
        $order = $order->with(['payments', 'skus'])->first();

        if ($order->status == 'pending') {
            $message = '';
            if ($order->data['payment']['type'] == 'bankslip') {
                $message = 'Atenção! Você ainda não efetuou o pagamento do boleto! Isso pode alterar o prazo de entrega do seu pedido! (Desconsidere essa mensagem se o pagamento já foi realizado. A compensação pode demorar até 2 dias úteis).';
            } else {
                $message = 'Atenção! Você ainda não efetuou o pagamento da sua compra! Isso pode alterar o prazo de entrega do seu pedido! (Desconsidere essa mensagem se optou por pagamento na retirada).';
            }
            AlertService::flash('warning', $message);
        }


        if (@$order->data["shipping_address"]["price"] && $order->data["shipping"]["type"] == "payment") {
            $route_tax =  number_format($order->data["shipping_address"]["price"], 2, ",", ".");
            $message = 'Atenção! O Valor de entrega por sua Rota é de R$ ' . $route_tax . '.<br>
                Esse valor é cobrado na hora do recebimento por cada entrega pelo carro da rota';

            AlertService::flash('warning', $message);
        }



        $order = $order;
        $shipping = OrderShipping::where("order_id", $order->id)->first();
        $status_list = StatusOrder::get();
        $status_payment_list = StatusOrderPayment::get();
        $status_item_list = StatusOrderItem::get()->pluck("name", "id");

        $timeline = new OrderTimeline($order);
        $steps = $timeline->getOrderTimeline();
        return view('pages.orders.detail', compact('order', 'status_list', 'status_payment_list', 'status_item_list', 'steps', 'shipping'));
    }

    public function OrdersView(Request $request)
    {
        $filter = ["filter" => $request->all()];
        $filter["filter"]["status"] = @$filter["filter"]["status"] ? $filter["filter"]["status"] : "todos";
        $counter = [
            "all" => Order::all()->count(),
            "forwarded" => Order::where("status_id", @StatusOrder::where("value", "forwarded")->first()->id)->count(),
            "pending" => Order::where("status_id", @StatusOrder::where("value", "pending")->first()->id)->count(),
            "approved" => Order::where("status_id", @StatusOrder::where("value", "approved")->first()->id)->count(),
            "production" => Order::where("status_id", @StatusOrder::where("value", "production")->first()->id)->count(),
            "finishing" => Order::where("status_id", @StatusOrder::where("value", "finishing")->first()->id)->count(),
            "canceled" => Order::where("status_id", @StatusOrder::where("value", "canceled")->first()->id)->count(),
            "finished" => Order::where("status_id", @StatusOrder::where("value", "finished")->first()->id)->count(),
        ];
        return view('pages.orders.index', compact('filter', 'counter'));
    }

    public function search(Request $request)
    {
        $query = Order::where("orders.id", ">", 0)->select("orders.*");
        $datatable = new Datatable($query);
        $datatable->setColumnsOrder(["orders.id", null, "orders.created_at", "DATE_FORMAT(orders.created_at,'%H:%i:%s')", null, "status_id", null, null, null]);
        $callback = function ($row) {
            $getStatus = function ($status) {
                switch (@$status->value) {
                    case ("pending"):
                        return "<span class='badge badge-pill badge-warning' style='background-color:#FBB254;color:white;font-size:12px'>" . $status->name . "</span>";
                        break;
                    case ("forwarded"):
                        return "<span class='badge badge-pill badge-info' style='background-color:#ff42e5;color:white;font-size:12px'>" . $status->name . "</span>";
                        break;
                    case ("approved"):
                        return "<span class='badge badge-pill badge-success' style='background-color:#009935;color:white;font-size:12px'>" . $status->name . "</span>";
                        break;
                    case ("production"):
                        return "<span class='badge badge-pill badge-warning' style='background-color:#94b2fc;color:white;font-size:12px'>" . $status->name . "</span>";
                        break;
                    case ("canceled"):
                        return "<span class='badge badge-pill badge-danger' style='background-color:#ed3838;color:white;font-size:12px'>" . $status->name . "</span>";
                        break;
                    case ("finished"):
                        return "<span class='badge badge-pill badge-danger' style='background-color:#0fe7ff;color:white;font-size:12px'>" . $status->name . "</span>";
                        break;
                    case ("finishing"):
                        return "<span class='badge badge-pill badge-danger' style='background-color:#a30fff;color:white;font-size:12px'>" . $status->name . "</span>";
                        break;
                    case ("on_shipment"):
                        return "<span class='badge badge-pill badge-danger' style='background-color:#00d3cc;color:white;font-size:12px'>" . $status->name . "</span>";
                        break;
                    default:
                        return  "<span class='badge badge-pill badge-default' style='background-color:#a5a4a4;color:white;font-size:12px'>" . @$status->name . "</span>";
                        break;
                }
            };
            $getTypePayment = function ($value) {
                switch ($value) {
                    case ("paylater"):
                        return "Pagar na Retirada";
                        break;
                    case ("creditcard"):
                        return "Cartão de Crédito";
                        break;
                    case ("bankslip"):
                        return "Boleto Bancário";
                        break;
                    default:
                        return $value;
                        break;
                }
            };

            $skus = $row->skus;
            $refs = "";
            $has_error = false;
            $payment_alert = "";
            $status_error =  StatusOrderItem::where("value", "with problems")->first();
            $payment = $row->payments->last();
            if ($payment) {   //dd($payment->data["payment"]["bankslip"]);
                if ($payment->status_id == StatusOrderPayment::where("value", "pending")->first()->id && (isset($payment->data["payment"]["bankslip"])))
                    $payment_alert = "<i class='material-icons mr-2 bank_slip_pending'>restore</i>";
                if ($payment->status_id == StatusOrderPayment::where("value", "approved")->first()->id)
                    $payment_alert = "<i class='material-icons mr-2 text-success approved'>check</i>";
                if ($payment->status_id == StatusOrderPayment::where("value", "canceled")->first()->id)
                    $payment_alert = "<i class='material-icons mr-2 text-danger payment_canceled'>close</i>";
                if ($payment->status_id == StatusOrderPayment::where("value", "reversed")->first()->id)
                    $payment_alert = "<i class='material-icons mr-2 text-danger payment_reversed'>replay</i>";
            }

            foreach ($skus as $sku) {
                $refs .= "<span class='badge badge-secondary mr-2' style='background-color:black;font-size:12px;'>" . $sku["data"]["config_info"]["ref_name"] . "</span>";
                if (!$has_error) {
                    if ($status_error->id == $sku->status_id) {
                        $has_error = true;
                    }
                }
            }
            $shipping_price = number_format(@$row["data"]["shipping"]["price"] ? @$row["data"]["shipping"]["price"] : 0, 2, ",", ".");
            $payment_type = @$payment->type;
            $order_id = @$payment->order_id;

            $data = [
                $has_error ? "<span class='d-flex align-items-center'>
                                    <a class='link d-none d-lg-block' href='" . route('order.view.detail', ["hashid" => $row->hashid]) . "'>#$row->code</a>
                                    <a class='link d-lg-none'>#$row->code</a>
                                    <i class='material-icons ml-2 text-danger error_tag'>warning</i>
                                </span>"
                    : "<span class='d-flex align-items-center'>
                                    <a class='link d-none d-lg-block' href='" . route('order.view.detail', ["hashid" => $row->hashid]) . "'>#$row->code</a>
                                    <a class='link d-lg-none'>#$row->code</a>
                                </span>",
                $refs,
                date_format($row->created_at, "d/m/Y"),
                date_format($row->created_at, "H:i:s"),
                $getStatus($row->status),
                "<span class='d-flex align-items-center'>" . $payment_alert . $getTypePayment($payment_type) . "</span>",
                $shipping_price > 0 ? "R$ " . ($row["data"]["shipping"]["price"] ? str_replace(".", ",", $shipping_price) : "00,00") : "Não Existe",
                "R$ " . number_format((float) @$row["data"]["totalPrice"] ? @$row["data"]["totalPrice"] : 0, 2, ',', ''),
                "<a  class='link' href='" . route('order.view.detail', ["hashid" => $row->hashid]) . "'><b class='text-right'><i class='ml-2 material-icons'>more_vert</i></b></a>",
                $order_id ? "<a  class='link' href='" . route("comprar_novamente.view", ["order_id" => $order_id]) . "'><b class='text-right'><i class='ml-2 material-icons'>more_vert</i></b></a>":
                "",
            ];
            return $data;
        };
        $filterLogic = function ($results, $filters) {
            if (@$filters["code"]) {
                $code = $filters["code"];
                $results = $results->whereRaw("CONCAT(DATE_FORMAT(orders.created_at, '%y'),LPAD(orders.id,6,0)) like '%$code%'");
            }
            // dd($filters);
            if (@$filters["status"]) {
                $status = $filters["status"];
                if ($status != "todos") {
                    switch ($status) {
                        case "pendente":
                            $status = "pending";
                            break;
                        case "encaminhado":
                            $status = "forwarded";
                            break;
                        case "aprovado":
                            $status = "approved";
                            break;
                        case "producao":
                            $status = "production";
                            break;
                        case "finalizando":
                            $status = "finishing";
                            break;
                        case "concluido":
                            $status = "finished";
                            break;
                        case "cancelado":
                            $status = "canceled";
                            break;
                    }
                    $status = StatusOrder::where("value", $status)->first();
                    $results = $results->where("status_id", @$status->id);
                }
                if (@$filters["art_name"]) {
                    $art_name = $filters["art_name"];
                    $order_ids = DB::connection("dashboard_server")->table("order_sku")->where("data->config_info->ref_name", "like", "%$art_name%")->pluck("order_id")->toArray();
                    $results = $results->whereIn("id", $order_ids);
                }
                if (@$filters["payment_method"]) {
                    $payment_method = $filters["payment_method"];
                    if ($payment_method != "all")
                        $results = $results->where("orders.data->payment->type", $payment_method);
                }
                if (@$filters["shipping_method"]) {
                    $shipping_method = $filters["shipping_method"];
                    if ($shipping_method != "all")
                        $results = $results->where("orders.data->shipping->type", $shipping_method);
                }
                if (@$filters["date_start"]) {
                    $date_start = $filters["date_start"];
                    $results = $results->whereDate("orders.created_at", ">=", Date($date_start));
                }
                if (@$filters["date_end"]) {
                    $date_end = $filters["date_end"];
                    $results = $results->whereDate("orders.created_at", "<=", Date($date_end));
                }
                if (@$filters["time_start"]) {
                    $time_start = $filters["time_start"];
                    $results = $results->whereTime("orders.created_at", ">=", $time_start);
                }
                if (@$filters["time_end"]) {
                    $time_end = $filters["time_end"];
                    $results = $results->whereTime("orders.created_at", "<=", $time_end);
                }
                if (@$filters["pagto_approved"]) {
                    $pagto_approved = $filters["pagto_approved"];
                    if ($pagto_approved == "on") {
                        $status_id = StatusOrderPayment::where("value", "approved")->first()->id;
                        $approved_ids = OrderPayment::where("status_id", $status_id)->pluck("order_id")->toArray();
                        $results = $results->whereIn("orders.id", $approved_ids);
                    }
                }
                if (@$filters["showing_day"]) {
                    $showing_day = str_replace("d", "", $filters["showing_day"]);
                    if ($showing_day != "all")
                        $results = $results->whereDate('orders.created_at', '>=', Carbon::today()->subDays(intval($showing_day)));
                } else {
                    $results = $results->whereDate('orders.created_at', '>=', Carbon::today()->subDays(15));
                }
            }
            return $results;
        };
        return $datatable->make($request, $callback, $filterLogic);
    }

    public function getTotal($data, $cart)
    {
        $subtotal = 0;
        foreach ($cart->getcontent() as $row) {
            $subtotal += (float) $row->price;
        }

        

        $shipping = $this->getShippingPrice($data, $cart, $subtotal);
        Log::debug('dataTotal->  '.json_encode($shipping));

        $result = (float) $shipping + (float) $subtotal;
        Log::debug('dataTotal2->  '.json_encode($result));
        return [
            'total' => $result,
            'shipping' => $shipping
        ];
    }

    private function getJadlogPriceApi($data, $cart, $subtotal)
    {
        $controller = app(JadlogController::class);

        $zipcode = $data['shipping_address']['zip_code'];
        $cartWeight = CartController::getCartWeight($cart);

        $shipping = $controller->useFreteCalcApi($subtotal, $cartWeight, $data["shipping_address"], 0)["calcs"];

        $index = $data['shipping']['method']; //index de qual item deverá ser acessado
        $time = $shipping[$index]["rawTime"];
        $shipping = $shipping[$index]['rawPrice'];
        $this->updateShippingTime($time);
        return $shipping;
    }

    public function getJadlogPrice($data, $cart, $subtotal)
    {
        if (config("fretecalc.active")) return $this->getJadlogPriceApi($data, $cart, $subtotal);

        Log::debug('dataShippping4->  '.json_encode($data));

        $request['street']           = $data['shipping_address']['street'];
        $request['number']           = $data['shipping_address']['number'];
        $request['district']         = $data['shipping_address']['district'];
        $request['city']             = $data['shipping_address']['city'];
        if(isset($data['shipping_address']['complement']))
        $request['complement']       = $data['shipping_address']['complement'];
        if(isset($data['shipping_address']['reference']))
        $request['reference']        = $data['shipping_address']['reference'];
        $request['zip_code']         = $data['shipping_address']['zip_code'];
        $request['name']             = $data['shipping_address']['name'];
        $request['vldeclarado']      = $subtotal;
        $request['peso']             = CartController::getCartWeight($cart);

        $jadlog  = new JadlogClass();
        if($data['shipping']['type']==="retirada_balcao"){
            $index = 11;
            $shipping = $jadlog->shippingCalcPickup($request, null, $index);
        }else{
            $index = $data['shipping']['method']; //index de qual item deverá ser acessado
            $shipping = $jadlog->shippingCalc($request, null, $index);
        }



        Log::debug('shippingCalc->  '.json_encode($shipping));
        $shipping = array_first($shipping['calcs'])['rawPrice'];
        return $shipping;
    }

    public function getWithdrawalPrice($data)
    {
        return 0;
    }

    public function getRoutePrice($data)
    {
        return 0;
    }

    public function getShippingPrice($data, $cart, $subtotal)
    {
        // Log::debug('dataShippping->  '.json_encode($data));
        // Log::debug('dataShippping2->  '.json_encode($cart));
        // Log::debug('dataShippping3->  '.json_encode($subtotal));
         switch ($data['shipping']['type']) {
             case 'withdrawal':
                 return $this->getWithdrawalPrice($data);
                 break;
             case 'payment': //rota
                 return $this->getRoutePrice($data);
                 break;
             default:
                 return $this->getJadlogPrice($data, $cart, $subtotal);
                 break;
         }
        //return $this->getJadlogPrice($data, $cart, $subtotal);
    }

    private function sanitizeCreditCard($data)
    {
        if (isset($data["number"])) {
            $data["cvv"] = "***";
            $data["expiring_date"] = "**/**";
            $number = $data["number"];
            $data["number"] = "**** **** **** *" . substr($number, (strlen($number) - 3), strlen($number));
        }
        return $data;
    }

    private function updateShippingTime($days = 0)
    {
        $this->shippingDelay += $days;
    }

    public function setShippingData($data, $prices)
    {

        $data['shipping']['price'] = $prices['shipping'];
        // $data['shipping']['id'] = "Rota"; //sera substituido se nao for rota

        Log::debug('dataShippping6->  '.json_encode($data));
        switch ($data['shipping']['type']) {
            case 'shipping':
                $data['shipping']['id'] = "F";
                break;
            case "client_shipping":
                $data['shipping']['id'] = "G";
                break;
            case 'withdrawal':
                $supply_store = SupplyStore::find($data['shipping_address']['id']);
                $this->updateShippingTime($supply_store['data']['shipping_delay']);
                $data['shipping_address'] = $supply_store->address;
                $data['shipping']['id'] = $supply_store->api_ref;
                break;
            case 'payment':

                $userAddresses = UserAddresses::where("user_id", Auth::user()->id)
                    ->where("type", "route")
                    ->first();

                $delay = Auth::user()->reseller->route->toArray();
                $this->updateShippingTime($delay['info']['shipping_delay']);

                $routeAddress = $this->setRouteAddress($userAddresses);
                $data['shipping_address'] = $routeAddress;
                $data['shipping']['id'] = "Rota";
                break;
        }

        return $data;
    }


    private function setRouteAddress($userAddresses)
    {

        $data["name"] = @$userAddresses->name;
        $data["street"] = @$userAddresses->street;
        $data["number"] = @$userAddresses->number;
        $data["district"] = @$userAddresses->district;
        $data["state"] = @$userAddresses->state;
        $data["city"] = @$userAddresses->city;
        $data["zip_code"] = @$userAddresses->zip_code;
        $data["complement"] = @$userAddresses->complement;
        $data["reference"] = @$userAddresses->reference;
        $data["price"] = @$userAddresses->data["route_tax"];

        return  $data;
    }



    private function registerOrderSkus($cart, $order)
    {
        $defaultOrder = StatusOrderItem::where("value", "waiting")->first();
        $items = [];

        foreach ($cart->getContent() as $row) {
            foreach ($row->attributes->items as $item) {
                $sku = Sku::find($item["sku"]["id"])->load("product")->toArray();
                $newItem = [
                    "group_ref" => $row->id,
                    "product_id" => $item["sku"]["product_id"],
                    "sku_id" => $item["sku"]["id"],
                    'qty' => $item['quantity'],
                    'total' => (float) $item['price'],
                    'status_id' => $defaultOrder->id,
                    'order_id' => $order->id,
                    'data' => [
                        'cart_id' => $row->id,
                        'quantity' => $item["quantity"],
                        'config_info' => $row->attributes["data"],
                        'art_creation_info' => $row->attributes["data"]["art_creation_info"],
                        'sku' => $sku,
                        'file' => (isset($row->attributes["data"]["upload"]) ? $row->attributes["data"]["upload"] : null)
                    ],
                ];
                $newItem["data"]["config_info"]["upload"]["send_date"] = Carbon::now()->format("Y-m-d H:i:s");
                $items[] = $newItem;
            }
        }
        $ids = [];
        foreach ($items as $item) {
            $order_sku = OrderSku::create($item);
            $ids[] = $order_sku->id;
        }
        return $ids;
    }

    public function getArrivalDate($data)
    {
        $shipping = $data['shipping'];
        $user = \Auth::user();
        $cartController = \App::make(CartController::class);
        $cart = $cartController->init($user->code);
        $prod_time = $cartController->longest_sku_preparation_time($cart);
        $time = $prod_time ? $prod_time : 1;
        $time += $this->shippingDelay;
        $days = $time;
        $time += isFactoryClosed();
        return ['date' => getNextBusinessDay($time), 'time' => $days];
    }

    private function registerOrder($data)
    {
        $user = Auth::user();
        $defaultSection = StatusOrder::where("value", "pending")->first();

        if (@isset($data["payment"]["data"])) {
            $data['payment']['data'] = $this->sanitizeCreditCard($data["payment"]["data"]);
        }
        $arrival = $this->getArrivalDate($data);
        $data['estimated_time'] = $arrival['time'];
        $data['arrival_date']   = null;
        $newOrder = ['data' => $data, 'log' => '[]', "user_id" => '7999', "user_data" => $user, "status_id" => $defaultSection->id];
        return Order::create($newOrder);
    }

    public function validateShippingAddress($request)
    {
        $address = @$request->shipping_address;
        if (@$address["type"] == "client" || @$address["type"] == "") {
            $exists = UserAddresses::find(@$address["id"]);
            if (@$address["client_doc"] == \Auth::user()->reseller->doc) {
                throw new \Exception("Você não pode usar seu CPF/CNPJ para Envio para Cliente");
            }
        }
    }

    public function store(Request $request)
    {
        try {
            $this->validateShippingAddress($request);

            $order_skus = null;
            DB::beginTransaction();
            $user = Auth::user();
            $cartController = App::make(CartController::class);
            $cart = $cartController->init($user->code);

            $data = $request->all();

            $prices = $this->getTotal($request, $cart);
            $data['totalPrice'] = $prices['total'];
            $data = $this->setShippingData($data, $prices);

            $order = $this->registerOrder($data);
            $data["orderId"] = $order->id;

            $this->registerOrderSkus($cart, $order);

            /**
             * @todo Implement payment
             */
            $paymentService = new PaymentService();
            $returnPayment = $paymentService->paymentRouter($order, $data);

            $paymentService = config('payment.payment_service');

            if(isset($data["payment"]["type"])){
                if($data["payment"]["type"] == "bankslip" || $data["payment"]["type"] == "paylater"){
                    $paymentService = "getnet";
                }
            }

            if ($paymentService == 'cielo') {
                if (empty($returnPayment)) {
                    throw new \Exception("", 1);
                }
                if (is_array($returnPayment)) {
                    $returnPayment = (object) $returnPayment;
                }
                if(!isset($returnPayment->ReturnCode)){
                    throw new \Exception($returnPayment->message, 1);
                }
            } else {

                if (@$returnPayment["success"] === false) {
                    throw new \Exception(@$returnPayment['message']);
                }
            }


            DB::commit();
            $cart->clear();
            $dash_request = new DashboardApi($order->id . "/send_email_new_order");

            $dash_request->make("new_order_email", []);
            $dash_request->send();

            

            if ($paymentService == 'cielo') {
                return [
                    'success' => true,
                    'message' => null,
                    'data' => $returnPayment
                ];
            } else {
                return [
                    'success' => true,
                    'message' => null,
                    'data' => [
                        'payment' => $returnPayment['data'],
                        'payment_data' => $returnPayment['payment'],
                        'message' => $returnPayment['message'],
                        'order' => $order
                    ]
                ];
            }
        } catch (\Exception $e) {
            if (@$order) {
                @$order->delete();
                //if (@$payment && @$payment['payment'] != null) @$payment['payment']->delete();
                if (@$order_skus) @OrderSku::whereIn('id', $order_skus)->delete();
            }
            session()->forget('shipping_data');
            report($e);
            @DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => '']);
        }
    }


    private function set_order_queue($id)
    {
        $client = new Client();
        $data = [
            "json"    => ["id" => $id]
        ];

        $guzzleReturn = $client->request("POST", env('DASHBOARD_ROUTE') . "/api/orders/queue/store", $data);
        $result = $guzzleReturn->getBody();
        $result = json_decode($result, true);
        debug_log("Order/Queue", "Enviando Order de ID $id para fila", [$data, $result]);
        $guzzleReturn->getStatusCode();
    }

    public function getGateway($request)
    {
        if (@$request['response']['payment_id']) {
            return "getnet";
        } else {
            return null;
        }
    }

    private function registerOrderPayment($order_id, $request, $paymentDate = null)
    {
        $status = StatusOrderPayment::where("value", "pending")->first();
        $data = [
            'order_id' => $order_id,
            'status_id' => $status->id,
            'data' => $request,
            'bankslip_id' => @$request['response']['boleto']['boleto_id'],
            'amount' => $request['amount'],
            'payment_id' => @$request['response']['payment_id'],
            'payment_date' => @$paymentDate,
            'gateway' => $this->getGateway($request),
            'log' => '[]'
        ];

        $payment = OrderPayment::create($data);
        return $payment;
    }

    private function paylater($order, $data)
    {
        $amount = preg_replace('/\s/', '', number_format($data['totalPrice'], 2, '', ' '));
        $address = $data['address'];
        $user = Auth::user();

        $request = [
            'amount' => (string) $amount,
            'orderId' => (string) $data['orderId'],
            'customerId' => (string) $user->id,
            'street' => (string) $address['street'],
            'number' => (string) $address['number'],
            'complement' => (string) $address['complement'],
            'district' => (string) $address['district'],
            'city' => (string) $address['city'],
            'country' => 'Brasil',
            'state' => (string) $address['state'],
            'name' => (string) $user->name,
            'documentType' => $user->reseller->getDocType(),
            'documentNumber' => (string) $this->removeMask($user->doc),
            'postalCode' => (string) $address['zip_code'],
            'payment' => ['paylater' => [], "confirmed" => true]
        ];
        $payment = $this->registerOrderPayment($data['orderId'], $request);
        return ['data' => true, 'payment' => $payment, 'message' => 'Pedido realizado com sucesso'];
    }

    private function removeMask($str)
    {
        return preg_replace('/[^A-z0-9]/', '', $str);
    }

    private function bankslip($order, $data)
    {
        $amount = preg_replace('/\s/', '', number_format($data['totalPrice'], 2, '', ' '));
        $address = $data['address'];
        $user = Auth::user();
        // $this->setHolderName($data['payment']);

        $request = [
            'amount' => (string) $amount,
            'orderId' => (string) $data['orderId'],
            'productType' => 'physical_goods',
            'customerId' => (string) $user->id,
            'street' => (string) $address['street'],
            'number' => (string) $address['number'],
            'complement' => (string) $address['complement'],
            'district' => (string) $address['district'],
            'city' => (string) $address['city'],
            'country' => 'Brasil',
            'state' => (string) $address['state'],
            //
            'delayed' => '1',
            'saveCardData' => '0',
            'transactionType' => '1',
            'numberInstallments' => '1',
            'cardNumber' => '',
            'cardholderName' => '',
            'expirationMonth' => '',
            'expirationYear' => '',
            'customerId' => '',
            'verifyCard' => 'false',
            'securityCode' => '',
            //
            'firstName' => (string) $user->reseller->name,
            'fullName' => (string) $user->reseller->full_name,
            'customerId' => (string) $user->id,
            'documentType' => $user->reseller->getDocType(),
            'documentNumber' => (string) $this->removeMask($user->reseller->doc),
            'postalCode' => (string) $address['zip_code'],
            'action' => '2', //boleto
        ];


        // $request = array_merge($request);
        $result = GetnetClass::makeTransaction($request);
        $response = $result->getnetResponse;
        if (!$response->success) {
            return ['data' => false, 'payment' => null, 'message' => @$response->message];
        }

        $request['payment'] = [
            'bankslip' => [
                'pdf' => $result->linkPdf,
                'html' => $result->linkHtml,
            ],
        ];
        $request['response'] = $result->response;
        $payment = $this->registerOrderPayment($data['orderId'], $request);

        $_data = $order->data;
        $_data["payment"]["confirmed"] = false;
        $order->data = $_data;
        $order->save();
        return [
            'data' => $result->getnetResponse->success,
            'payment' => $payment,
            'message' => 'Pedido realizado, porém o pagamento do boleto ainda está pendente, aguardando pagamento ...'
        ];
    }

    public function setHolderName($data)
    {
        $this->holder = [
            'firstName' => $data["data"]["address"]['firstName'],
            'lastName'  => $data["data"]["address"]['lastName'],
            'fullName'  => $data["data"]["address"]['firstName'] . " " . $data["data"]["address"]['lastName']
        ];
    }

    private function creditcard($order, $data)
    {
        $this->setHolderName($data['payment']);

        $user = Auth::user();

        $card = $data['payment']['data'];
        $card['expiring_date'] = explode('/', $card['expiring_date']);

        $installment = $card['installment'];
        $transactionType = ($installment > 1) ? 'INSTALL_NO_INTEREST' : 'FULL';

        $data['totalPrice'] = ($card['installment'] > 1) ? $this->getInterest($card['installment'], $data['totalPrice']) : $data['totalPrice'];

        $amount = preg_replace('/\s/', ' ', number_format($data['totalPrice'], 2, '', ''));

        $request = [
            'amount' => (string) $amount,
            'orderId' => (string) $data['orderId'],
            'productType' => 'physical_goods',
            'cardId' => @$data["payment"]["data"]["card_id"] ? @$data["payment"]["data"]["card_id"] : null,
            'customerId' => (string) $user->id,
            'documentType' => $data['payment']['data']['documentType'],
            'documentNumber' => preg_replace("/\D+/", "", $data['payment']['data']['documentNumber']),
            'email' => $data['payment']['data']['email'],
            'phoneNumber' => preg_replace("/\D+/", "", $data['payment']['data']['phoneNumber']),

            'street' => (string) $data['payment']["data"]['address']['street'],
            'number' => (string) $data['payment']["data"]['address']['number'],
            'complement' => (string) $data['payment']["data"]['address']['complement'],
            'district' => (string) $data['payment']["data"]['address']['district'],
            'city' => (string) $data['payment']["data"]['address']['city'],
            'country' => 'Brasil',
            'state' => (string) $data['payment']["data"]['address']['state'],
            'postalCode' => (string) preg_replace("/\D+/", "", $data['payment']["data"]['address']['postalCode']),

            'delayed' => '1',
            'saveCardData' => '0',
            'numberInstallments' => $installment,
            'transactionType' => $transactionType,

            'cardNumber' => str_replace(' ', '', $card['number']),
            'cardholderName' => (string) @$card['name'],
            'expirationMonth' => (string) @$card['expiring_date'][0],
            'expirationYear' => (string) @$card['expiring_date'][1],
            'customerId' => (string) $user->id,
            'verifyCard' => 'false',
            'securityCode' => (string) $card['cvv'],

            'action' => '0', //credito
            "save_card" => (@$data["payment"]["data"]["save_card"] ? $data["payment"]["data"]["save_card"] : false),
            'firstName'  => (string) $data['payment']["data"]['address']['firstName'],
            'lastName'   => (string) $data['payment']["data"]['address']['lastName'],
        ];
        $request = array_merge($request, $this->holder);

        $result = GetnetClass::makeTransaction($request);
        $response = @$result->response;
        if (!@$result->getResponse()->success) {
            $message = "Transação Negada. Houve um erro ao tentar efetuar o pagamento. Entre em contato com a sua Operadora de Crédito";
            return ['success' => false, "message" => $message, "data" => null];
        }


        $confirmed = ($result->getTransactionStatus() == 'APPROVED');

        $request['payment'] = [
            'creditcard' => [
                'approved' => $confirmed,
            ],
            "confirmed" => $confirmed
        ];

        $request = array_except($request, ['expirationMonth', 'expirationYear', 'securityCode', 'cardNumber']);
        $request = array_merge($request, $order->data['payment']['data']);


        $request['response'] = $response;
        if ($confirmed) {
            $paymentDate = Carbon::now()->format('Y-m-d H:i:s');
            $payment = $this->registerOrderPayment($data['orderId'], $request, $paymentDate);
        } else {
            return [
                'data' => false,
                'message' => 'Transação Negada. Houve um erro ao tentar efetuar o pagamento. Contate o suporte para mais detalhes'
            ];
        }

        $new_order_info = $order->data;
        $new_order_info["payment"]["confirmed"] = $confirmed; //manter esse confirmed
        $order->data = $new_order_info;
        $order->save();
        return ['data' => true, 'payment' => $payment, 'message' => ($confirmed ? 'Transação efetuada com sucesso' : 'Ocorreu um erro durante a transação. O pedido será processado independentemente. Contate o suporte para mais detalhes')];
    }


    public function createJadlogOrder($data, $cart)
    {
        $cart =  $this->prepareForCart($cart);
        $controller = \App::make(JadlogController::class);

        $order = $controller->createJadlogData($data, $cart);
        $jadlog = new JadlogClass();
        return [
            'ref_id' => $controller->ref_id,
            'result' => $jadlog->getData($order, 'create')
        ];
    }


    public function prepareForCart($cart)
    {
        $content = $cart->getContent();
        $content = CartResource::collection($content);
        $content = $content->toArray(request());

        return $content;
    }

    private function checkOrderCanCancel($item, $data)
    {
        $order = Order::findOrFail($item->order_id);

        $price = floatval($data["price"]);
        if ($price >= 1000) return ['success' => false, 'message' => "Devido ao Valor do Pedido, é necessário entrar em contato com o Atendimento da Padrão Color para efetuar o Cancelamento", 'data' => null];

        $shipping_type = $order->data["shipping"]["type"];
        $payment_type = $order->payments[0]->type;
        if (
            $order->api_integration == "SENT" && (
                (($shipping_type == "payment") && ($payment_type == "paylater")) || (($shipping_type == "withdrawal") && ($payment_type == "paylater")) || (($shipping_type == "retirada_balcao") && ($payment_type == "paylater")) || ((in_array($shipping_type, ["shipping", "client_shipping"])) && ($payment_type == "bankslip")))
        ) {
            $time  = Carbon::now()->toDateTimeString();
            $order_time = $item->updated_at->addMinutes(15)->toDateTimeString();
            if ($order_time < $time) {
                return ['success' => false, 'message' => "Este pedido não pode ser cancelado pois já foi enviado para produção", 'data' => null];
            }
        }
        return ['success' => true];
    }

    public function OrderItemCancel(Request $request)
    {
        try {
            $data = $request->all();
            $item = OrderSku::findOrFail($data["order_sku"]);

            $checkCan = $this->checkOrderCanCancel($item, $data);
            if (!$checkCan["success"]) return response()->json($checkCan);

            $dash_request = new DashboardApi($item->order_id);
            $dash_request->make("order_cancel", [
                "cancel_reason" => $data["reason"],
                "item_id" => $item->id
            ]);
            $dash_request->send();
            $response = $dash_request->getResponse();
            return $response;
        } catch (\Exception $e) {
            debug_log("Order/Item/Cancel", $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => $e->getTrace()]);
        }
    }
}
