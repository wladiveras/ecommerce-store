<?php

namespace ismaelgr\getnet\services;

use ismaelgr\getnet\config\AuthGetNet;
use ismaelgr\getnet\config\Environment;
use ismaelgr\getnet\request\GetNetResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Log;


/**
 * Class DebitCard.
 */
class BankSlip
{
    /** @var Environment $environment */
    private $environment;

    /** @var GetNetResponse $getnetResponse */
    public $getnetResponse;

    /** @var string $customerId */
    private $customerId;

    /** @var string $customerHolderName */
    private $customerHolderName;

    /** @var Auth $auth */
    private $auth;

    /** @var string $numberToken */
    private $numberToken;

    /** @var string $linkPdf */
    public $linkPdf;

    /** @var string $linkPdf */
    public $linkHtml;

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

    /** @var bool $documentNumber */
    private $documentNumber;

    /** @var bool $documentType */
    private $documentType;

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

        return  config('getnet.' . config('app.env') . '.seller_id');
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
    public function getNumberToken()
    {
        return $this->numberToken;
    }

    /**
     * Tokenize Card.
     *
     * @return $this
     */
    public function registerPayment()
    {
        $this->createBankSlip();

        return $this;
    }

    /**
     * Create Bank Slip.
     *
     * @return $this
     */
    private function createBankSlip()
    {

        $url = $this->environment->getApiUrl() . 'v1/payments/boleto';

        $auth = $this->auth->makeAuth();
        $client = new Client();
        $docNumber = str_pad($this->orderId, 15, '0', STR_PAD_LEFT);

        $headers = [
            'Authorization' => $auth->getTokenType() . ' ' . $auth->getAccessToken(),
            'Content-Type' => 'application/json',

        ];



        $params = [
            'seller_id' => $this->sellerId,
            'amount' => $this->amount,
            'currency' => 'BRL',
            'order' => [
                'order_id' => $this->orderId,
                'sales_tax' => 0,
                'product_type' => $this->productType,
            ],

            'boleto' => [
                'document_number' => $docNumber,
                'instructions' => 'Não receber após o vencimento',
                'provider' => 'santander',
                'expiration_date' => now()->addDays(3)->format("d/m/Y")
            ],

            'customer' => [
                'first_name' => $this->firstName,
                'name' =>  $this->fullName,
                'document_type' => $this->documentType,
                'document_number' => $this->documentNumber,
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
        ];

        $array['headers'] = $headers;
        $array['body'] = json_encode($params);


        try {
            $guzzleReturn = $client->request('POST', $url, $array);

            $return = json_decode($guzzleReturn->getBody(), true);

            $this->linkPdf = $this->environment->getApiUrl() . $return['boleto']['_links'][0]['href'];
            $this->linkHtml = $this->environment->getApiUrl() . $return['boleto']['_links'][1]['href'];
            $this->response = $return;
            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');
        } catch (BadResponseException $e) {

            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $e->getMessage());
            Log::debug($this->getnetResponse->getMessage());
        }
        return $this;
    }

    private function setValue($Request)
    {
        $this->sellerId = $this->getSellerId();
        $this->amount = $Request['amount'];
        $this->orderId = $Request['orderId'];
        $this->productType = $Request['productType'];
        $this->customerId = $Request['customerId'];
        $this->firstName = $Request['firstName'];
        $this->fullName = $Request['fullName'];
        $this->street = $Request['street'];
        $this->number = $Request['number'];
        $this->complement = $Request['complement'];
        $this->district = $Request['district'];
        $this->city = $Request['city'];
        $this->state = $Request['state'];
        $this->country = $Request['country'];
        $this->delayed = $Request['delayed'];
        $this->saveCardData = $Request['saveCardData'];
        $this->transactionType = $Request['transactionType'];
        $this->postalCode = $Request['postalCode'];
        $this->numberInstallments = $Request['numberInstallments'];
        $this->cardholderName = $Request['cardholderName'];
        $this->expirationMonth = $Request['expirationMonth'];
        $this->documentNumber = $Request['documentNumber'];
        $this->documentType = $Request['documentType'];
    }
}
