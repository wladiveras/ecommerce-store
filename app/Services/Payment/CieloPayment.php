<?php

namespace App\Services\Payment;

use App\Services\Payment\Cielo\BankSlipController;
use App\Services\Payment\Cielo\CreditCardController;
use Auth;
use App\Models\UserCard;

use GuzzleHttp\Client;
use App\Models\StatusOrderPayment;
use App\Models\OrderPayment;
use Illuminate\Http\Request;

class CieloPayment
{

    private $creditCardCielo;
    private $bankSlipCielo;

    public function __construct()
    {
        $this->creditCardCielo = new CreditCardController();
        $this->bankSlipCielo = new BankSlipController();
    }

    public function getDataErrorCielo($returned)
    {
        if ($returned->data == "") {
            return "";
        }

        if (!property_exists($returned->data, 'errors')) {
            return "";
        }

        if (!property_exists($returned->data->errors, 'cielo')) {
            return "";
        }

        if (sizeOf($returned->data->errors->cielo) == 0) {
            return "";
        }

        if (!array_key_exists(0, $returned->data->errors->cielo)) {
            return "";
        }

        return $returned->data->errors->cielo[0];
    }

    public function payment($order, $data)
    {
        try {
            $paymentReturn = $this->pay($order, $data);

            if (empty($paymentReturn)) {
                return ['success' => false, 'data' => false, 'payment' => null, 'message' => "Algo de errado durante o pagamento, não foi possível finalizar a compra."];
            }

            if(isset($paymentReturn->Mensagem)){
                return ['success' => false, 'data' => false, 'payment' => null, 'message' => $paymentReturn->Mensagem, 'MerchantOrderId' => $paymentReturn->MerchantOrderId];
            }

            if(isset($paymentReturn->ReturnCode)){
                if($paymentReturn->ReturnCode != "4" && $paymentReturn->ReturnCode != "6" && $paymentReturn->ReturnCode != "00"){
                    return ['success' => false, 'data' => false, 'payment' => null, 'message' => $paymentReturn->ReturnMessage . "Código: " . $paymentReturn->ReturnCode, 'MerchantOrderId' => false];
                    //return ['success' => false, 'data' => false, 'payment' => null, 'message' => "Algo de errado durante o pagamento, não foi possível finalizar a compra.", 'MerchantOrderId' => false];
                }
            }

            if (is_object($paymentReturn)) {
                if (property_exists($paymentReturn, 'errors')) {
                    if ($paymentReturn->success == false) {
                        if ($paymentReturn->message == "") {
                            $paymentReturn->message = $this->getDataErrorCielo($paymentReturn);
                            if (!$paymentReturn->message == "") {
                                return $paymentReturn;
                            }
                        }

                        \Log::debug(json_encode($paymentReturn));

                        return ['success' => false, 'data' => false, 'payment' => null, 'message' => "Algo de errado durante o pagamento, não foi possível finalizar a compra."];
                    }
                }
            }

            if (is_array($paymentReturn)) {
                $paymentReturn = (object) $paymentReturn;
            }

            //dd($paymentReturn);

            if ($paymentReturn->ProofOfSale) {

                $this->set_order_queue($order->id);

                if ($data['payment']['type'] != "paylater") {

                    $status = StatusOrderPayment::where("value", "approved")->first();
                    OrderPayment::where('order_id', $order->id)->update(['status_id' => $status->id]);
                }
            }

            return $paymentReturn;
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
        }
    }

    private function pay($order, $data)
    {

        switch ($data['payment']['type']) {
            case 'creditcard':
                return $this->creditcard($order, $data);
            case 'bankslip':
                return $this->bankslip($order, $data);
            case 'paylater':
                return $this->paylater($order, $data);
        }

        return false;
    }

    private function creditcard($order, $data)
    {
        $creditCardType = config('cielo.cielo_creditcard');
        $cieloCreditCard = new CreditCardController();

        $cieloCreditCardReturn = null;
        switch ($creditCardType) {
            case 'simple':
                $cieloCreditCardReturn = $cieloCreditCard->creditcardSimple($order, $data);
                break;
            case 'full':
                $cieloCreditCardReturn = $cieloCreditCard->creditcardFull($order, $data);
                break;
            case 'antifraud':
                $cieloCreditCardReturn = $cieloCreditCard->creditcardAntiFraud($order, $data);
                break;
            default:
                throw new Exception("Cielo Credit Card Type is unknown", 1);
                break;
        }

        if (is_object($cieloCreditCardReturn)) {
            if (isset($cieloCreditCardReturn->Mensagem)) {
                return $cieloCreditCardReturn;
            }

            return null;
        }

        $cieloData = $cieloCreditCardReturn[1];

        $purchaseReturnData = $cieloData;

        $paymentDate = \Carbon::now()->format('Y-m-d H:i:s');

        $prepareDataResult = $this->prepareData($cieloCreditCardReturn);

        $this->registerOrderPaymentCreditCard($purchaseReturnData, $prepareDataResult, $paymentDate);

        $this->storeCardCreditAfterPurchase($purchaseReturnData);

        return $cieloCreditCardReturn[0];
    }

    private function bankSlip($order, $data)
    {
        $user = Auth::user();
        $cieloBankslip = new BankSlipController();
        $cieloBankSlipReturn = $cieloBankslip->bankSlip($user, $data);

        //dd($cieloBankSlipReturn);

        if (empty($cieloBankSlipReturn)) {
            return null;
        }

        if (is_array($cieloBankSlipReturn)) {
            if (empty($cieloBankSlipReturn[0])) {
                return null;
            }
        }

        if (is_object($cieloBankSlipReturn)) {
            if (property_exists($cieloBankSlipReturn->data, 'errors')) {
                return $cieloBankSlipReturn;
            }

            return null;
        }

        $cieloBankSlipRequest = $cieloBankSlipReturn[1];
        $cieloBankSlipReturn = $cieloBankSlipReturn[0];

        $request['response'] = $result->response;
        $payment = $this->registerOrderPaymentBankSlip($data['orderId'], $request);

        $_data = $order->data;
        $_data["payment"]["confirmed"] = false;
        $order->data = $_data;
        $order->save();
        return [
            'data' => $result->getnetResponse->success,
            'payment' => $payment,
            'message' => 'Pedido realizado, porém o pagamento do boleto ainda está pendente, aguardando pagamento ...'
        ];

        return $cieloBankSlipReturn[0];
    }

    private function prepareData($cieloCreditCardReturn)
    {
        // {"amount":"44460","orderId":"36219","customerId":"929","street":"Rua Vi\u00fava Dantas","number":"717","complement":"","district":"Campo Grande","city":"Rio De Janeiro","country":"Brasil","state":"RJ","name":"Nova","documentType":"CNPJ","documentNumber":"39932702000149","postalCode":"23052090","payment":{"paylater":[],"confirmed":true}}

        $cieloData = $cieloCreditCardReturn[1];
        $purchaseReturnData = $cieloData;

        $userId = Auth::user()->getId();
        return [
            "amount" => $purchaseReturnData['payment']['amount'],
            "orderId" => $purchaseReturnData['merchantOrderId'],
            "customerId" => $userId,
            "street" => $purchaseReturnData['customer']['deliveryAddress']['street'],
            "number" => $purchaseReturnData['customer']['deliveryAddress']['number'],
            "complement" => $purchaseReturnData['customer']['deliveryAddress']['complement'],
            "district" => "",
            "city" => $purchaseReturnData['customer']['deliveryAddress']['city'],
            "country" => $purchaseReturnData['customer']['deliveryAddress']['country'],
            "state" => $purchaseReturnData['customer']['deliveryAddress']['street'],
            "name" => $purchaseReturnData['customer']['name'],
            'installments' => $purchaseReturnData['payment']['installments'],
            "documentType" =>  "",
            "documentNumber" => "",
            "postalCode" => $purchaseReturnData['customer']['deliveryAddress']['zipCode'],
            "payment" => [
                "creditcard" => [
                    "approved" => true
                ],
                "confirmed" => true
            ]
        ];
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
        $payment = $this->registerOrderPaymentPaylater($data['orderId'], $request);
        return ["success" => true, 'data' => true, 'payment' => $payment, 'message' => 'Pedido realizado com sucesso'];
    }

    private function removeMask($str)
    {
        return preg_replace('/[^A-z0-9]/', '', $str);
    }

    private function registerOrderPaymentCreditCard($request, $prepareDataResult, $paymentDate = null)
    {
        $status = StatusOrderPayment::where("value", "pending")->first();
        $data = [
            'order_id' => $request['merchantOrderId'],
            'status_id' => $status->id,
            'data' => $prepareDataResult,
            'bankslip_id' => "",
            'amount' => $request['payment']['amount'],
            'payment_id' => @$request['payment']['paymentId'],
            'payment_date' => @$paymentDate,
            'gateway' => "cielo",
            'log' => '[]'
        ];

        $payment = OrderPayment::create($data);
        return $payment;
    }

    public function storeCardCreditAfterPurchase($saleInfo)
    {
        if (!array_key_exists('payment', $saleInfo)) {
            return false;
        }

        $payment = $saleInfo['payment'];

        if (!array_key_exists('creditCard', $payment)) {
            return false;
        }

        $card = $payment['creditCard'];

        if (!array_key_exists('cardToken', $card)) {
            return false;
        }

        if (empty($card['cardToken'])) {
            return false;
        }

        if (empty($card['cardNumber'])) {
            return false;
        }

        if (!array_key_exists("customer", $saleInfo)) {
            return false;
        }

        $customer = $saleInfo['customer'];

        if (!array_key_exists("address", $customer)) {
            return false;
        }

        $address = $customer['address'];

        if (empty($address)) {
            return false;
        }

        $addressJSON = json_encode($address);

        $userCard = new UserCard();
        $userCard->user_id            = Auth::user()->id;
        $userCard->card_id            = $card['cardToken'];
        $userCard->last_four_digits   = substr($card['cardNumber'], -4);;
        $userCard->brand              = $card["brand"];
        $userCard->billing_address    = $addressJSON;
        $userCard->gateway = "cielo";
        $userCard->saveOrFail();
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

    public function getCreditCardSaved(Request $request)
    {
        $user = Auth::user();
        $card = UserCard::where("user_id", $user->id)->where("card_id", $request['cardId'])->first();
        return $card;
    }

    private function registerOrderPaymentBankSlip($request, $prepareDataResult, $paymentDate = null)
    {
        $status = StatusOrderPayment::where("value", "pending")->first();
        $data = [
            'order_id' => $request['sale']['merchantOrderId'],
            'status_id' => $status->id,
            'data' => $prepareDataResult,
            'bankslip_id' => "",
            'amount' => $request['sale']['payment']['amount'],
            'payment_id' => @$request['sale']['payment']['paymentId'],
            'payment_date' => @$paymentDate,
            'gateway' => "cielo",
            'log' => '[]'
        ];

        $payment = OrderPayment::create($data);
        return $payment;
    }

    private function registerOrderPaymentPaylater($order_id, $request, $paymentDate = null)
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
            'gateway' => "getnet",
            'log' => '[]'
        ];

        $payment = OrderPayment::create($data);
        return $payment;
    }
}
