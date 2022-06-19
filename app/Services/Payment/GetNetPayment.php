<?php

namespace App\Services\Payment;

use App;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderSku;
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
use App\Http\Controllers\Cielo\CreditCardController;
use App\Http\Controllers\Cielo\BankSlipController;
use Exception;

class GetNetPayment
{
    public function payment($order, $data)
    {
        $payment = $this->pay($order, $data);
        if (!$payment['data'] || ($data['payment']['type'] == "bankslip" && !@$payment["data"] && !@$payment->success) || (@$payment->data != null && !@$payment->success)) {
            return $payment;
        }
        if (@$payment["payment"]["data"]["payment"]["confirmed"]) {

            $this->set_order_queue($order->id);

            if ($data['payment']['type'] != "paylater") {

                $status = StatusOrderPayment::where("value", "approved")->first();
                OrderPayment::where('order_id', $order->id)->update(['status_id' => $status->id]);
            }
        }

        return $payment;
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

    public function setHolderName($data)
    {
        $this->holder = [
            'firstName' => $data["data"]["address"]['firstName'],
            'lastName'  => $data["data"]["address"]['lastName'],
            'fullName'  => $data["data"]["address"]['firstName'] . " " . $data["data"]["address"]['lastName']
        ];
    }

    public function getInterest($installments, $totalPrice)
    {
        switch ($installments) {
            case 2:
                $result = $totalPrice + (($totalPrice / 100) * 4.02);
                break;

            case 3:
                $result = $totalPrice + (($totalPrice / 100) * 4.56);
                break;
        }

        return $result;
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
            'gateway' => "getnet",
            'log' => '[]'
        ];

        $payment = OrderPayment::create($data);
        return $payment;
    }

    private function bankslip($order, $data)
    {
        $user = Auth::user();

        $amount = preg_replace('/\s/', '', number_format($data['totalPrice'], 2, '', ' '));
        $address = $data['address'];

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


    private function removeMask($str)
    {
        return preg_replace('/[^A-z0-9]/', '', $str);
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

    private function set_order_queue($id)
    {
        try {
            $client = new Client();
            $data = [
                "json"    => ["id" => $id]
            ];

            $guzzleReturn = $client->request("POST", env('DASHBOARD_ROUTE') . "/api/orders/queue/store", $data);
            $result = $guzzleReturn->getBody();
            $result = json_decode($result, true);
            debug_log("Order/Queue", "Enviando Order de ID $id para fila", [$data, $result]);
            $guzzleReturn->getStatusCode();
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
        }
    }
}
