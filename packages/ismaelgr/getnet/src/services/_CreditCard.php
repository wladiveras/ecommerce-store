<?php

namespace ismaelgr\getnet\services;

use ismaelgr\getnet\config\AuthGetNet;
use ismaelgr\getnet\config\Environment;
use GuzzleHttp\Client;
use ismaelgr\getnet\request\GetNetResponse;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Log;
use App\Models\UserCard;
use Auth;


/**
 * Class CreditCard.
 */
class CreditCard
{
    /** @var Environment $environment */
    private $environment;

    /** @var GetNetResponse $getnetResponse */
    public $getnetResponse;

    /** @var Auth $auth */
    private $auth;

    private $cardNumber;

    /** @var string $brand */
    private $brand;

    /** @var string $customerId */
    private $customerId;

    /** @var string $customerHolderName */
    private $customerHolderName;

    /** @var string $expirationMonth */
    private $expirationMonth;

    /** @var string $expirationYear */
    private $expirationYear;

    /** @var bool $verifyCard */
    private $verifyCard;

    /** @var string $securityCode */
    private $securityCode;

    /** @var string $numberToken */
    private $numberToken;

    /** @var string $cardId */
    private $cardId;

    /** @var string $verificationId */
    private $verificationId;

    /** @var string $authorizationCode */
    private $authorizationCode;

    /** @var string $seller_id */
    private $sellerId;

    /** @var string $amount */
    private $amount;

    /** @var string $order_id */
    private $orderId;

    /** @var string $product_type */
    private $productType;

    /** @var string $name */
    private $name;

    /** @var string $street */
    private $street;

    /** @var string $number */
    private $number;

    /** @var string $complement */
    private $complement;

    /** @var string $complement */
    private $district;

    /** @var string $city */
    private $city;

    /** @var string $state */
    private $state;

    /** @var string $state */
    private $country;

    /** @var string $postal_code */
    private $postalCode;

    /** @var bool $delayed */
    private $delayed;

    /** @var bool $save_card_data */
    private $saveCardData;

    /** @var string $transaction_type */
    private $transactionType;

    /** @var string $number_installments */
    private $numberInstallments;

    /** @var string $cardholder_name */
    private $cardholderName;

    /** @var string $trasnsactionStatus */
    private $transactionStatus;

    /** @var json $billingAddress */
    private $billingAddress;

    /**
     * Auth constructor.
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment, $Request)
    {

        $this->setValue($Request);

        $clientId = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $this->environment = $environment;

        $auth = new AuthGetNet($clientId, $clientSecret, $this->environment);
        $this->setAuth($auth);
    }

    public function getSellerId()
    {
        return config('getnet.' . config('app.env') . '.seller_id');
    }

    public function getClientId()
    {
        return config('getnet.' . config('app.env') . '.client_id');
    }

    public function getClientSecret()
    {
        return config('getnet.' . config('app.env') . '.client_secret');
    }

    /**
     * Gets result of auth in api.
     *
     * @return GetNetResponse
     */
    public function getResponse()
    {
        return $this->getnetResponse;
    }

    /**
     * @param $cardNumber
     *
     * @return $this
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * @param $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * @param $customerHolderName
     *
     * @return $this
     */
    public function setCustomerHolderName($customerHolderName)
    {
        $this->customerHolderName = $customerHolderName;

        return $this;
    }

    /**
     * @param $expirationMonth
     *
     * @return $this
     */
    public function setExpirationMonth($expirationMonth)
    {
        $this->expirationMonth = $expirationMonth;

        return $this;
    }

    /**
     * @param $expirationYear
     *
     * @return $this
     */
    public function setExpirationYear($expirationYear)
    {
        $this->expirationYear = $expirationYear;

        return $this;
    }

    /**
     * @param $verifyCard
     *
     * @return $this
     */
    public function setVerifyCard($verifyCard)
    {
        $this->verifyCard = $verifyCard;

        return $this;
    }

    /**
     * @param $securityCode
     *
     * @return $this
     */
    public function setSecurityCode($securityCode)
    {
        $this->securityCode = $securityCode;

        return $this;
    }

    /**
     * @param Auth $auth
     *
     * @return $this
     */
    public function setAuth(AuthGetNet $auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * @return string
     */
    public function getNumberToken()
    {
        return $this->numberToken;
    }

    /**
     * Tokenize Card.
     *
     * @return $this
     */
    public function tokenizeCard()
    {
        if (!$this->cardId) {

            $this->createNumberToken();
            $this->response = $this->createCard();
        } else { //cartão salvo
            $this->safeBox("getCard", $this->cardId);
            $this->response = $this->finishTransaction($this->auth->getTokenType(), $this->auth->getAccessToken());
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * Create token number.
     *
     * @return $this
     */
    private function createNumberToken()
    {
        $url = $this->environment->getApiUrl() . 'v1/tokens/card';
        $auth = $this->auth->makeAuth();
        $client = new Client();

        try {
            $guzzleReturn = $client->request('POST', $url, [
                'headers' => [

                    'Authorization' => $auth->getTokenType() . ' ' . $auth->getAccessToken(),
                    'Content-Type' => 'application/json;application/x-www-form-urlencoded',
                    'seller_id' => $this->sellerId,
                ],
                'body' => json_encode([
                    'card_number' =>  $this->cardNumber,
                    'customer_id' => $this->customerId,
                ]),
            ]);

            $return = json_decode($guzzleReturn->getBody(), true);

            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');
            $this->numberToken = $return['number_token'];
        } catch (BadResponseException $e) {

            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $e->getResponse()->getBody()->getContents());
            debug_log("Getnet/Creditcard", "Create Number Token", [json_encode($this->getnetResponse), request()->ip()]);
            return ["error" => true, "message" => "Dados do cartão inválidos"];
        }

        return $this;
    }

    /**
     * Create card number.
     *
     * @return $this
     */
    private function createCard()
    {
        $card = $this->createNumberToken();
        if (is_array($card)) {
            if (@$card["error"]) {
                $this->getnetResponse = new GetNetResponse(false, 402, $card["message"]);
                return $this->getnetResponse;
            }
        }
        $url = $this->environment->getApiUrl() . 'v1/cards/verification';
        $auth = $card->auth;

        $client = new Client();

        try {
            $jsonRequest = [
                'number_token' => $this->numberToken,
                'brand' => $this->brand,
                'cardholder_name' => $this->cardholderName,
                'expiration_month' => $this->expirationMonth,
                'expiration_year' => $this->expirationYear,
                'security_code' => $this->securityCode,
            ];
            $guzzleReturn = $client->request(
                'POST',
                $url,
                [
                    'headers' => [
                        'Authorization' => $auth->getTokenType() . ' ' . $auth->getAccessToken(),
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode($jsonRequest),
                ]
            );

            $return = json_decode($guzzleReturn->getBody(), true);

            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');

            $this->verificationId = $return['verification_id'];
            $this->authorizationCode = $return['authorization_code'];
            return $this->finishTransaction($auth->getTokenType(), $auth->getAccessToken());
        } catch (BadResponseException $e) {
            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $e->getResponse()->getBody()->getContents());
            debug_log("Getnet/Creditcard", "Create Card ERROR", [json_encode($this->getnetResponse), request()->ip()]);
        }
    }

    public function finishTransaction($tokenType, $accessToken)
    {
        $headers = [
            'Authorization' => $tokenType . ' ' . $accessToken,
            "Content-Type" => "application/json"
        ];

        $params = [
            'seller_id' => $this->sellerId,
            'amount' => $this->amount,
            "currency" => "BRL",
            'order' =>
            [
                'order_id' => $this->orderId,
                "sales_tax" => 0,
                'product_type' => $this->productType,
            ],

            'customer' => [
                'customer_id' => $this->customerId,
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'name' => $this->fullName,
                'email' => $this->email,
                'document_type' => $this->documentType,
                'document_number' => $this->documentNumber,
                'phone_number' => "55" . $this->phoneNumber,

                'billing_address' => [
                    'street' => $this->street,
                    'number' => $this->number,
                    'complement' => $this->complement,
                    'district' => $this->district,
                    'city' => $this->city,
                    'state' => $this->state,
                    'country' => $this->country,
                    'postal_code' => $this->postalCode,
                ],
            ],

            'device' => [
                'ip_address' => env("APP_ENV") == "homologation" ? "142.4.205.165" : request()->ip(),
                'device_id' => session('fingerprint'),
            ],

            'credit' => [
                "authenticated" => false,
                "pre_authorization" => false,
                'delayed' => false,
                'save_card_data' => false,
                'transaction_type' => $this->transaction_type, //'FULL', //INSTALL_WITH_INTEREST
                'number_installments' => $this->numberInstallments,
                'soft_descriptor' => "PadraoColor Id" . $this->orderId,
                "dynamic_mcc" => 1301,
                'card' => [
                    'number_token' => $this->numberToken,
                    'cardholder_name' => $this->cardholderName,
                    'security_code' => $this->securityCode ? $this->securityCode : null,
                    'expiration_month' => (string) $this->expirationMonth,
                    'expiration_year' => (string) $this->expirationYear
                ],
            ],
        ];

        $array['headers'] = $headers;
        $array['body'] = json_encode($params);

        $url = $this->environment->getApiUrl() . 'v1/payments/credit';

        $client = new Client();

        try {

            $guzzleReturn = $client->request('POST', $url, $array);
            $return = json_decode($guzzleReturn->getBody(), true);
            $this->transactionStatus = $return['status'];

            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');
            debug_log("Getnet/Creditcard", "Finish Transaction SUCESS", [$return, request()->ip()]);
            if ($this->save_card) {
                $card = $this->saveCreditCard();
                $this->storeCardCredit($card);
            }
            return $return;
        } catch (BadResponseException $e) {

            $response = new GetNetResponse(false, $e->getCode(), $e->getResponse()->getBody()->getContents());

            $message = json_decode($response->getMessage());
            $message = @$message->details[0]->description ? $message->details[0]->description : str_replace("!", "", $message->details[0]->antifraud->description . " Pelo Antifraude") . "!";
            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $message);
            debug_log("Getnet/Creditcard", "Finish Transaction ERROR", [$message, request()->ip()]);
        }
    }





    public function saveCreditCard()
    {

        $card = $this->createNumberToken();
        $url = $this->environment->getApiUrl() . 'v1/cards';
        $auth = $this->auth;
        $client = new Client();

        try {

            $guzzleReturn = $client->request(
                'POST',
                $url,
                [
                    'headers' => [
                        'Authorization' => $auth->getTokenType() . ' ' . $auth->getAccessToken(),
                        'Content-Type' => 'application/json',
                        'seller_id' =>  $this->sellerId

                    ],
                    'body' => json_encode([
                        'number_token' => $this->numberToken,
                        'brand' =>  $this->brand,
                        'cardholder_name' => $this->cardholderName,
                        'expiration_month' =>  $this->expirationMonth,
                        'expiration_year' => $this->expirationYear,
                        'customer_id' => $this->customerId,
                        'cardholder_identification' => $this->documentNumber,
                        'verify_card' => false,
                        'security_code' => $this->securityCode,
                    ]),
                ]
            );

            $return = json_decode($guzzleReturn->getBody(), true);
            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');

            return $return;
        } catch (BadResponseException $e) {

            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $e->getResponse()->getBody()->getContents());
            debug_log("Getnet/Creditcard", "Save CreditCard ERROR", [json_encode($this->getnetResponse), request()->ip()]);
        }
    }

    public function safeBox($action, $id)
    {
        $card = $this->createNumberToken();
        $url = $this->environment->getApiUrl() . 'v1/cards/' . $id;
        $auth = $this->auth;
        $client = new Client();
        $method = $this->getMethod($action);

        try {

            $guzzleReturn = $client->request(
                $method,
                $url,
                [
                    'headers' => [
                        'Authorization' => $auth->getTokenType() . ' ' . $auth->getAccessToken(),
                        'Content-Type' => 'application/json',
                        'seller_id' =>  $this->sellerId
                    ],

                ]
            );

            $return = json_decode($guzzleReturn->getBody(), true);
            $this->numberToken     = $return["number_token"];
            $this->brand           = $return["brand"];
            $this->cardholderName  = $return["cardholder_name"];
            $this->expirationMonth = $return["expiration_month"];
            $this->expirationYear  = $return["expiration_year"];

            $card = UserCard::where("user_id", Auth::user()->id)->where("card_id", $id)->first();

            $this->documentType = @$card->billing_address["documentType"];
            $this->documentNumber = @$card->billing_address["documentNumber"];
            $this->phoneNumber = @$card->billing_address["phoneNumber"];
            $this->email = @$card->billing_address["email"];

            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');

            return $return;
        } catch (BadResponseException $e) {
            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $e->getResponse()->getBody()->getContents());
            debug_log("Getnet/Creditcard", "Safebox ERROR", [json_encode($this->getnetResponse), request()->ip()]);
        }
    }



    private function setValue($Request)
    {
        $this->sellerId = $this->getSellerId();
        $this->amount = @$Request['amount'];
        $this->orderId = @$Request['orderId'];
        $this->cardNumber = (@$Request['cardNumber']) ? $Request['cardNumber'] : "5155901222280001";
        $this->productType = @$Request['productType'];

        $this->customerId = @$Request['customerId'];
        $this->firstName = @$Request['firstName'];
        $this->lastName = @$Request['lastName'];
        $this->fullName = @$Request['fullName'];
        $this->email = @$Request['email'];
        $this->documentType = @$Request['documentType'];
        $this->documentNumber = @$Request['documentNumber'];
        $this->phoneNumber = @$Request['phoneNumber'];

        $this->street = @$Request['street'];
        $this->number = @$Request['number'];
        $this->complement = @$Request['complement'];
        $this->district = @$Request['district'];
        $this->city = @$Request['city'];
        $this->state = @$Request['state'];
        $this->country = @$Request['country'];
        $this->delayed = @$Request['delayed'];
        $this->saveCardData = @$Request['saveCardData'];

        $this->transactionType = @$Request['transactionType'];
        $this->postalCode = @$Request['postalCode'];
        $this->cardholderName = @$Request['cardholderName'];
        $this->expirationMonth = @$Request['expirationMonth'];
        $this->expirationYear = @$Request['expirationYear'];
        $this->securityCode = @$Request['securityCode'];
        $this->numberInstallments = @$Request['numberInstallments'];
        $this->transaction_type = @$Request['transactionType'];
        $this->save_card = @$Request['save_card'];
        $this->billingAddress = [
            "street"     => @$Request['street'],
            "number"     => @$Request['number'],
            "complement" => @$Request['complement'],
            "district"   => @$Request['district'],
            "city"       => @$Request['city'],
            "country"    => @$Request['country'],
            "state"      => @$Request['state'],
            "postalCode" => @$Request['postalCode'],
            "firstName"  => @$Request['firstName'],
            "lastName"   => @$Request['lastName'],
            "fullName"       => @$Request['fullName'],
            "documentType"   => @$Request['documentType'],
            "documentNumber" => @$Request['documentNumber'],
            "phoneNumber"    => @$Request['phoneNumber'],
            "email"          => @$Request['email']
        ];
        $this->cardId = @$Request["cardId"] ?  $Request["cardId"] : null;
        // dd($Request);
    }

    private function storeCardCredit($card)
    {
        $data = $this->safeBox("getCard", $card['card_id']);
        $userCard = new UserCard();
        $userCard->user_id            = Auth::user()->id;
        $userCard->card_id            = $card['card_id'];
        $userCard->last_four_digits   = $data["last_four_digits"];
        $userCard->brand              = $data["brand"];
        $userCard->billing_address    = $this->billingAddress;
        $userCard->gateway            = "getnet";

        $userCard->saveOrFail();
    }

    private function getMethod($action)
    {
        switch ($action) {
            case "getCard";
                return "GET";
                break;
            case "deleteCard";
                return "DELETE";
                break;
        }
    }
}
