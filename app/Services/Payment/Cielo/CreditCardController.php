<?php

namespace App\Services\Payment\Cielo;

use App;
use Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CartController;
use stdClass;
use App\Models\UserCard;
use GuzzleHttp\Exception\RequestException;

class CreditCardController extends Controller
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

    public function creditcardSimple($order, $data)
    {

        try {
            $dataToSend = $this->fillCreditcardSimple($order, $data);
            if (!is_array($dataToSend)) {
                return $this->brandInvalidError();
            }
            $client = new \GuzzleHttp\Client();
            $res = $client->request(
                'POST',
                config('cielo.cielo_url') . '/credit-card/simple/payment',
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

    public function creditcardFull($order, $data)
    {
        try {

            if (!array_key_exists('card_id', $data['payment']['data'])) {
                return $this->purchaseWithCreditCardFull($order, $data);
            }

            if (!empty($data['payment']['data']['card_id'])) {
                return $this->purchaseWithTokenFull($order, $data);
            } else {
                return $this->purchaseWithCreditCardFull($order, $data);
            }
        } catch (\Exception $e) {
            return $this->UnknownError();
        }
    }

    public function purchaseWithTokenFull($order, $data)
    {
        $dataToSend = $this->fillPurchaseFullWithToken($order, $data);
        $client = new \GuzzleHttp\Client();
        $res = $client->request(
            'POST',
            config('cielo.cielo_url') . '/credit-card/full/payment/token',
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
    }

    public function purchaseWithCreditCardFull($order, $data)
    {
        $dataToSend = $this->fillCreditCardFull($order, $data);
        $client = new \GuzzleHttp\Client();
        $urlCielo = config('cielo.cielo_url') . config('cielo.cielo_endpoint_venda');
        $cieloMerchantId = config('cielo.cielo_merchantId');
        $cieloMerchantKey = config('cielo.cielo_merchantKey');
        try{
        $res = $client->request(
            'POST',
             $urlCielo ,
            [
                "headers" => [
                    'MerchantId' => [$cieloMerchantId],
                    'MerchantKey' => [$cieloMerchantKey],
                    'Content-Type' => "application/json"
                ],
                'json' => $dataToSend,
                'http_errors' => true,
            ]
        );
        } catch (\GuzzleHttp\Exception\RequestException $e) {
           $response = $e->getResponse();
           $responseBodyAsString = $response->getBody()->getContents();
           $listaErros = json_decode($responseBodyAsString);
           if(!empty($listaErros)){
              $erro = reset($listaErros);
              if(isset($erro->Code)){
                  //$codigoErroCielo = $this->codigosErro($erro->Code);
                  $codigoErroCielo = $erro->Message;
                  $retorno = (object)[];
                  $retorno->Mensagem = $codigoErroCielo;
                  $retorno->MerchantOrderId = false;
                  return $retorno;
              }
           }
        }

        if ($res->getStatusCode() != 200 && $res->getStatusCode() != 201) {
            $resposta = $res->getResponse();
            throw new \Exception("teste");
        }

        $resBody = (string) $res->getBody();
        $resBodyJSON = json_decode($resBody);
        if(isset($resBodyJSON) && isset($resBodyJSON->Payment)){
            $idPagamento = $resBodyJSON->Payment->PaymentId;
            $codigoRetorno = $resBodyJSON->Payment->ReturnCode;
            //if($codigoRetorno != "00" && $codigoRetorno != "4" && $codigoRetorno != "6"){
              //  return [$resBodyJSON, $dataToSend];
            //}
            try{
            $resCaptura = $client->request(
                'PUT',
                 $urlCielo . $idPagamento . "/capture",
                [
                    "headers" => [
                        'MerchantId' => [$cieloMerchantId],
                        'MerchantKey' => [$cieloMerchantKey],
                    ],
                    'http_errors' => true,
                ]
            );
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $listaErros = json_decode($responseBodyAsString);
                if(!empty($listaErros)){
                   $erro = reset($listaErros);
                   if(isset($erro->Code)){
                       //$codigoErroCielo = $this->codigosErro($erro->Code);
                       $codigoErroCielo = $erro->Message;
                       $retorno = (object)[];
                       $retorno->Mensagem = $codigoErroCielo;
                       $retorno->MerchantOrderId = false;
                       return $retorno;
                   }
                }
             }

            if ($resCaptura->getStatusCode() != 200 && $resCaptura->getStatusCode() != 201) {
                $respostaCaptura = $resCaptura->getResponse();
                throw new \Exception("teste");
            }

            $resBodyCaptura = (string) $resCaptura->getBody();
            $resBodyJSONCaptura = json_decode($resBodyCaptura);
            return [$resBodyJSONCaptura, $dataToSend];
        }

    }

    private function codigosErro($codigo)
    {
        $mensagens = ['146' => 'Código de segurança deve conter 3 caracteres',
                      '126' => 'Data de vencimento do cartão inválida',
                      '999' => 'Data inválida',
                      '129' => 'Meio de pagamento não vinculado a loja ou Provider inválido',
                      '167' => 'Antifraude não vinculado ao cadastro do lojista',
                    ];

        if(!isset($mensagens[$codigo])){
           return "Houve um problema com o pagamento";
        }
        return $mensagens[$codigo];
    }

    public function creditcardAntiFraud($order, $data)
    {
        try {
            $dataToSend = $this->fillCreditCardAntiFraud($order, $data);
            $client = new \GuzzleHttp\Client();
            $res = $client->request(
                'POST',
                config('cielo.cielo_url') . '/credit-card/anti-fraud/payment',
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
            $resBodyJSON = json_decode($resBody, true);
            return [$resBodyJSON, $dataToSend];
        } catch (\Exception $e) {
            return $this->UnknownError();
        }
    }

    public function fillCreditCardSimple($order, $data)
    {
        $userData = json_decode($order->getAttributes()['user_data'], true);
        $cardNumber = $this->getCardNumber($data);
        $brand = $this->getBrand($cardNumber);
        if (empty($brand)) {
            return $this->brandInvalidError();
        }
        return [
            "payment" => [
                "creditCard" => [
                    "cardNumber" => $cardNumber,
                    "expirationDate" => $this->getExpirationDate($data),
                    "brand" => $brand,
                    "securityCode" => $this->getSecurityCode($data),
                    "holder" => $this->getHolder($data)
                ],
                "installments" => $this->getInstallments($data),
                "amount" =>  $this->getTotalPrice($data),
            ],
            "merchantOrderId" => $this->getOrderId($data),
            "customer" => [
                "name" => $this->getCustomerName($userData),
            ]
        ];
    }

    public function fillPurchaseFullWithToken($order, $data)
    {
        $userData = json_decode($order->getAttributes()['user_data'], true);

        return [
            "merchantOrderId" => $this->getOrderId($data),
            "payment" => $this->fillPaymentFullWithToken($data),
            "customer" => $this->fillCustomerFullToken($userData, $data)
        ];
    }

    public function fillCreditCardFull($order, $data)
    {
        $userData = json_decode($order->getAttributes()['user_data'], true);
        $cardNumber = $this->getCardNumber($data);

        return [
            "merchantOrderId" => $this->getOrderId($data),
            "payment" => $this->fillPaymentFull($data),
            "customer" => $this->fillCustomerFull($userData, $data)
        ];
    }

    public function fillCreditCardAntiFraud($order, $data)
    {
        $userData = json_decode($order->getAttributes()['user_data'], true);
        $cardNumber = $this->getCardNumber($data);

        return [
            "merchantOrderId" => $this->getOrderId($data),
            "customer" => $this->fillCustomerAntiFraud($userData, $data),
            "payment" => $this->fillPaymentAntiFraud($data, $order, $userData)
        ];
    }

    public function fillCustomerFull($userData, $data)
    {
        return [
            "name" => $this->getCustomerName($userData),
            "status" => "",
            "identity" => preg_replace("/\D+/", "", $data['payment']['data']['documentNumber']),
            'identityType' => $data['payment']['data']['documentType'],
            "email" => $data['payment']['data']['email'],
            "birthdate" => "",
            "address" => [
                "street" => $data['payment']['data']["address"]['street'],
                "number" => (string) $data['payment']['data']["address"]['number'],
                "complement" => $data['payment']['data']["address"]['complement'],
                "zipCode" => (string) $data['payment']['data']["address"]['postalCode'],
                "city" => $data['payment']['data']["address"]['city'],
                "state" => $data['payment']['data']["address"]['state'],
                "country" => $data['payment']['data']["address"]['country'],
            ],
            "deliveryAddress" => [
                "street" => $data["shipping_address"]['street'],
                "number" => (string) $data["shipping_address"]['number'],
                "complement" => $data["shipping_address"]['complement'],
                "zipCode" => (string) $data["shipping_address"]['zip_code'],
                "city" => $data["shipping_address"]['city'],
                "state" => $data["shipping_address"]['state'],
                "country" => "Brasil",
            ]
        ];
    }

    public function fillCustomerFullToken($userData, $data)
    {
        return [
            "name" => $this->getCustomerName($userData),
            "status" => "",
            "identity" => preg_replace("/\D+/", "", $data['payment']['data']['documentNumber']),
            'identityType' => $data['payment']['data']['documentType'],
            "email" => $data['payment']['data']['email'],
            "birthdate" => "",
            "address" => [
                "street" => $data["address"]['street'],
                "number" => (string) $data["address"]['number'],
                "complement" => $data["address"]['complement'],
                "zipCode" => (string) $data["address"]['zip_code'],
                "city" => $data["address"]['city'],
                "state" => $data["address"]['state'],
                "district" =>  $data["address"]['district'],
                "country" => "Brasil",
            ],
            "deliveryAddress" => [
                "street" => $data["shipping_address"]['street'],
                "number" => (string) $data["shipping_address"]['number'],
                "complement" => $data["shipping_address"]['complement'],
                "zipCode" => (string) $data["shipping_address"]['zip_code'],
                "city" => $data["shipping_address"]['city'],
                "state" => $data["shipping_address"]['state'],
                "country" => "Brasil",
            ]
        ];
    }

    public function fillPaymentFull($data)
    {
        $cardNumber = $this->getCardNumber($data);
        return [
            "softDescriptor" => "Apel " . $this->getOrderId($data),
            "serviceTaxAmount" => 0,
            "installments" => $this->getInstallments($data),
            "interest" => "ByMerchant",
            "authenticate" => false,
            "creditCard" => [
                "cardNumber" => $cardNumber,
                "holder" => $this->getHolder($data),
                "expirationDate" => $this->getExpirationDate($data),
                "securityCode" => $this->getSecurityCode($data),
                "saveCard" => (@$data["payment"]["data"]["save_card"] ? true : false),
                "brand" => $this->getBrand($cardNumber)
            ],
            "isCryptoCurrencyNegotiation" => false,
            "type" => "CreditCard",
            "amount" =>  $this->getTotalPrice($data),
            "airlineData" => [
                "ticketNumber" => ""
            ]
        ];
    }

    public function fillPaymentFullWithToken($data)
    {
        $cardToken = $this->getCreditCardToken($data);
        return [
            "softDescriptor" => "Apel " . $this->getOrderId($data),
            "serviceTaxAmount" => 0,
            "installments" => $this->getInstallments($data),
            "interest" => "ByMerchant",
            "authenticate" => false,
            "creditCard" => [
                "cardToken" => $cardToken,
                "securityCode" => $this->getSecurityCode($data),
                "saveCard" => (@$data["payment"]["data"]["save_card"] ? true : false),
                "brand" => $this->getBrandByToken($cardToken),
                "cardOnFile" => []
            ],
            "isCryptoCurrencyNegotiation" => false,
            "type" => "CreditCard",
            "amount" =>  $this->getTotalPrice($data),
            "airlineData" => [
                "ticketNumber" => ""
            ]
        ];
    }

    public function fillCustomerAntiFraud($userData, $data)
    {
        return [
            "name" => $this->getCustomerName($userData),
            "status" => "",
            "identity" => preg_replace("/\D+/", "", $data['payment']['data']['documentNumber']),
            'identityType' => $data['payment']['data']['documentType'],
            "email" => $data['payment']['data']['email'],
            "birthdate" => "",
            "mobile" => $this->getCustomerMobile($userData),
            "phone" => "",
            "address" => [
                "street" => $data['payment']['data']["address"]['street'],
                "number" => (string) $data['payment']['data']["address"]['number'],
                "complement" => $data['payment']['data']["address"]['complement'],
                "zipCode" => (string) $data['payment']['data']["address"]['postalCode'],
                "city" => $data['payment']['data']["address"]['city'],
                "state" => $data['payment']['data']["address"]['state'],
                "country" => $data['payment']['data']["address"]['country'],
                "district" => (string) $data['payment']["data"]['address']['district'],
            ],
            "deliveryAddress" => [
                "street" => $data["shipping_address"]['street'],
                "number" => (string) $data["shipping_address"]['number'],
                "complement" => $data["shipping_address"]['complement'],
                "zipCode" => (string) $data["shipping_address"]['zip_code'],
                "city" => $data["shipping_address"]['city'],
                "state" => $data["shipping_address"]['state'],
                "country" => "Brasil",
                "district" => $data['shipping_address']['district']
            ],
            "billingAddress" => [
                "street" => $data['payment']['data']["address"]['street'],
                "number" => (string) $data['payment']['data']["address"]['number'],
                "complement" => $data['payment']['data']["address"]['complement'],
                "zipCode" => (string) $data['payment']['data']["address"]['postalCode'],
                "city" => $data['payment']['data']["address"]['city'],
                "state" => $data['payment']['data']["address"]['state'],
                "country" => $data['payment']['data']["address"]['country'],
                "district" => (string) $data['payment']["data"]['address']['district'],
            ]
        ];
    }

    public function fillPaymentAntifraud($data, $order, $userData)
    {
        $cardNumber = $this->getCardNumber($data);
        return [
            "serviceTaxAmount" => 0,
            "installments" => (int) $this->getInstallments($data),
            "interest" => "ByMerchant",
            "authenticate" => false,
            "softDescriptor" => "Apel " . $this->getOrderId($data),
            "amount" =>  $this->getTotalPrice($data),
            "currency" => "BRL",
            "country" => "BRA",
            "creditCard" => [
                "cardNumber" => $cardNumber,
                "holder" => $this->getHolder($data),
                "expirationDate" => $this->getExpirationDate($data),
                "securityCode" => $this->getSecurityCode($data),
                "saveCard" => (@$data["payment"]["data"]["save_card"] ? $data["payment"]["data"]["save_card"] : false),
                "brand" => $this->getBrand($cardNumber)
            ],
            "fraudAnalysis" => $this->getFraudAnalysis($data, $order, $userData)
        ];
    }

    public function getCardNumber($data)
    {
        return preg_replace('/\s+/', '', $data['payment']['data']['number']);
    }

    public function getCreditCardToken($data)
    {
        return $data['payment']['data']['card_id'];
    }

    public function getExpirationDate($data)
    {
        $expiringDateObj = \DateTime::createFromFormat('m/y', $data['payment']['data']["expiring_date"]);
        return $expiringDateObj->format('m/Y');
    }

    public function getSecurityCode($data)
    {
        return $data['payment']['data']['cvv'];
    }

    public function getHolder($data)
    {
        return $data['payment']['data']['name'];
    }

    public function getInstallments($data)
    {
        return $data['payment']['data']["installment"];
    }

    public function getTotalPrice($data)
    {
        $totalPrinceInCents = number_format($data['totalPrice'], 2, '', '');
        $installment = $data['payment']['data']["installment"];
        if ($installment > 1) {
            return $this->getTotalPriceInstallments($installment, $data["totalPrice"]);
        } else {
            return $totalPrinceInCents;
        }
    }

    public function getTotalPriceInstallments($installments, $totalPrice)
    {
        switch ($installments) {
            case 2:
                $result = $totalPrice + (($totalPrice / 100) * 4.02);
                break;

            case 3:
                $result = $totalPrice + (($totalPrice / 100) * 4.56);
                break;
        }

        return number_format($result, 2, '', '');
    }

    public function getOrderId($data)
    {
        return $data["orderId"];
    }

    public function getCustomerName($data)
    {
        return $data['name'];
    }

    public function getBrand($data)
    {
        try {
            $cardNumber = substr($data, 0, 6);
            $client = new \GuzzleHttp\Client();
            $finalUrl = config('cielo.cielo_url_consulta') . config('cielo.cielo_endpoint_consulta_cartao') . $cardNumber;
            $res = $client->request('GET', $finalUrl, [
                "headers" => [
                    'MerchantId' => [config('cielo.cielo_merchantId')],
                    'MerchantKey' => [config('cielo.cielo_merchantKey')]
                ],
                'http_errors' => false,
            ]);

            if ($res->getStatusCode() != 200 && $res->getStatusCode() != 201) {
                return json_decode($res->getBody());
            }

            $bodyRes = json_decode($res->getBody());

            $brands = [
                "VISA" => "visa",
                "MASTERCARD" => 'Master',
                "AMEX" => 'Amex',
                "ELO" => 'Elo',
                "AURA" => "Aura",
                "JCB" => "JCB",
                "DINERS" => "Diners",
                "DISCOVER" => "Discover",
                "HIPERCARD" => "Hipercard"
            ];

            if (!array_key_exists($bodyRes->Provider, $brands)) {
                return null;
            }

            return $brands[$bodyRes->Provider];
        } catch (\Exception $e) {
            return $this->UnknownError();
        }
    }

    public function getBrandByToken($cardId)
    {
        $user = Auth::user();
        $userCard = UserCard::where("user_id", $user->id)->where("card_id", $cardId)->first();

        return $userCard->brand;
    }

    public function getCustomerMobile($userData)
    {
        return preg_replace("/\D+/", "", $userData['wpp_phone']);
    }

    public function getCustomerPhone($data)
    {
        return preg_replace("/\D+/", "", $data['payment']['data']['phoneNumber']);
    }

    public function getBrowserName()
    {
        $ExactBrowserNameUA = $_SERVER['HTTP_USER_AGENT'];

        if (strpos(strtolower($ExactBrowserNameUA), "safari/") and strpos(strtolower($ExactBrowserNameUA), "opr/")) {
            // OPERA
            $ExactBrowserNameBR = "Opera";
        } elseif (strpos(strtolower($ExactBrowserNameUA), "safari/") and strpos(strtolower($ExactBrowserNameUA), "chrome/")) {
            // CHROME
            $ExactBrowserNameBR = "Chrome";
        } elseif (strpos(strtolower($ExactBrowserNameUA), "msie")) {
            // INTERNET EXPLORER
            $ExactBrowserNameBR = "Internet Explorer";
        } elseif (strpos(strtolower($ExactBrowserNameUA), "firefox/")) {
            // FIREFOX
            $ExactBrowserNameBR = "Firefox";
        } elseif (strpos(strtolower($ExactBrowserNameUA), "safari/") and strpos(strtolower($ExactBrowserNameUA), "opr/") == false and strpos(strtolower($ExactBrowserNameUA), "chrome/") == false) {
            // SAFARI
            $ExactBrowserNameBR = "Safari";
        } else {
            // OUT OF DATA
            $ExactBrowserNameBR = "Unknown";
        };

        return $ExactBrowserNameBR;
    }

    public function getFraudAnalysis($data, $order, $userData)
    {
        $shipping = @$data["shipping"]["type"] == "withdrawal" ? "Pickup" : "OneDay";
        $user = Auth::user();
        $cartController = App::make(CartController::class);
        $cart = $cartController->init($user->code);
        return [
            "provider" => "cybersource",
            "sequence" => "AuthorizeFirst",
            "sequenceCriteria" => "OnSuccess",
            "captureOnLowRisk" => false,
            "voidOnHighRisk" => false,
            "totalOrderAmount" => $this->getTotalPrice($data),
            "fingerPrintId" => "074c1ee676ed4998ab66491013c565e2",
            "browser" => [
                "cookiesAccepted" => true,
                "email" => $data['payment']['data']['email'],
                "hostName" => "",
                "ipAddress" => $_SERVER['REMOTE_ADDR'],
                "type" => $this->getBrowserName()
            ],
            "cart" => [
                "isGift" => false,
                "returnsAccepted" => true,
                "items" => $this->getCartItems($cart)
            ],
            "shipping" => [
                "addressee" => $this->getCustomerName($userData),
                "method" => $shipping,
                "phone" => $this->getCustomerMobile($userData)
            ]
        ];
    }

    public function getCartItems($cart)
    {
        $items = [];
        foreach ($cart->getcontent() as $row) {
            $rowData = $row->all();
            $itemsRow = $rowData['attributes']->get("items");
            $product = $itemsRow[0]['product'];
            $quantity = $row->quantity;
            $item = new stdClass;
            $sku = $itemsRow[0]['sku'];
            $price = $sku['price'];

            $item->giftCategory = "Off";
            $item->hostHedge = "Normal";
            $item->nonSensicalHedge = "Off";
            $item->obscenitiesHedge = "Off";
            $item->phoneHedge = "Normal";
            $item->name = $product['name'];
            $item->quantity = $quantity;
            $item->sku = $sku['id'];
            $item->unitPrice = $price;
            $item->risk = "Normal";
            $item->timeHedge = "Off";
            $item->type = "Default";
            $item->velocityHedge = "High";

            $items[] = $item;
        }

        return $items;
    }

    public function fillCreditCardToToken($data)
    {
        $cardNumber = $this->getCardNumber($data);
        $brand = $this->getBrand($cardNumber);
        if (empty($brand)) {
            return $this->brandInvalidError();
        }
        return [
            "creditCard" => [
                "cardNumber" => $cardNumber,
                "expirationDate" => $this->getExpirationDate($data),
                "brand" => $brand,
                "securityCode" => $this->getSecurityCode($data),
                "holder" => $this->getHolder($data)
            ]
        ];
    }
}
