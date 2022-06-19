<?php

namespace App\Services\Payment\Cielo;

use App\Http\Controllers\Controller;

use stdClass;

class BankSlipController extends Controller
{

    public function brandInvalidError()
    {
        $returnResult = new stdClass;
        $returnResult->success = false;
        $returnResult->status = 400;
        $returnResult->message = "card brand is invalid";
        $returnResult->data = null;
        return $returnResult;
    }

    public function UnknownError()
    {
        $returnResult = new stdClass;
        $returnResult->success = false;
        $returnResult->status = 500;
        $returnResult->message = "error unknown";
        $returnResult->data = null;
        return $returnResult;
    }

    public function bankSlip($user, $data)
    {
        try {
            $dataToSend = [
                "merchantOrderId" => $this->getOrderId($data),
                "customer" => $this->fillCustomer($user, $data),
                "payment" => $this->fillPayment($data)
            ];

            //dd($dataToSend);

            $client = new \GuzzleHttp\Client();
            $res = $client->request(
                'POST',
                config('cielo.cielo_url') . '/bank-slip/payment',
                [
                    "headers" => [
                        'MerchantId' => [config('cielo.cielo_merchantId')],
                        'MerchantKey' => [config('cielo.cielo_merchantKey')]
                    ],
                    'json' => $dataToSend,
                    'http_errors' => false,
                ]
            );

            if ($res->getStatusCode() != 200 && $res->getStatusCode() != 201) {
                return json_decode((string) $res->getBody());
            }

            $resBody = (string) $res->getBody();
            $resBodyJSON = json_decode($resBody);
            return [$resBodyJSON, $dataToSend];
        } catch (\Exception $e) {
            return $this->UnknownError();
        }
    }

    public function fillCustomer($user, $data)
    {
        return [
            "name" => $this->getCustomerName($user),
            "identity" => (string) $this->removeMask($user->reseller->doc),

            "status" => "",
            "email" => $user->reseller->email,
            "birthdate" => "",
            "address" => [
                "street" => $data["address"]['street'],
                "number" => (string) $data["address"]['number'],
                "complement" => $data["address"]['complement'],
                "zipCode" => (string) $data["address"]['zip_code'],
                "city" => $data["address"]['city'],
                "state" => $data["address"]['state'],
                "country" => "Brasil",
                "district" => (string) $data["address"]['district']
            ]
        ];
    }

    public function fillPayment($data)
    {
        return [
            "type" => "Boleto",
            "amount" => $this->getTotalPrice($data),
            "provider" => "Bradesco",
            "address" => "",
            "boletoNumber" => "",
            "assignor" => "",
            "demonstrative" => "",
            "expirationDate" => "",
            "identification" => "",
            "instructions" => "Não receber após o vencimento"
        ];
    }

    public function getOrderId($data)
    {
        return $data["orderId"];
    }

    public function getCustomerName($user)
    {
        return (string) $user->reseller->full_name;
    }

    public function getTotalPrice($data)
    {
        return number_format($data['totalPrice'], 2, '', '');
    }

    private function removeMask($str)
    {
        return preg_replace('/[^A-z0-9]/', '', $str);
    }
}
