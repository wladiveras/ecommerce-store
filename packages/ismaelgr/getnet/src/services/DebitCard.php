<?php

namespace ismaelgr\getnet\services;

use ismaelgr\getnet\AuthGetNet;
use ismaelgr\getnet\Environment;
use ismaelgr\getnet\request\GetNetResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class DebitCard.
 */
class DebitCard
{
    /** @var Environment $environment */
    private $environment;

    /** @var GetNetResponse $getnetResponse */
    private $getnetResponse;

    /** @var string $cardNumber */
    private $cardNumber;

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

    /** @var Auth $auth */
    private $auth;

    /** @var string $numberToken */
    private $numberToken;

    /** @var string $cardId */
    private $cardId;

    /** @var string $verificationId */
    private $verificationId;

    /** @var string $authorizationCode */
    private $authorizationCode;

    /** @var string $paymentId */
    private $paymentId;

    /** @var string $payer_authentication_request */
    private $payerAuthentication;

    /**
     * Auth constructor.
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment, $Request)
    {
        $this->cardNumber = $Request['card_number'];
        $this->cardholderName = $Request['cardholder_name'];
        $this->expirationMonth = $Request['expiration_month'];
        $this->expirationYear = $Request['expiration_year'];
        $this->customerId = $Request['customer_id'];
        $this->securityCode = $Request['security_code'];

        $this->environment = $environment;
        $clientId = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $environment = $this->environment;

        $auth = new AuthGetNet($clientId, $clientSecret, $environment);

        $this->setAuth($auth);
    }

    public function getSellerId()
    {   
        return config('getnet.'.config('app.env').'.seller_id');
    }

    public function getClientId()
    {
        return config('getnet.'.config('app.env').'.client_id');
    }

    public function getClientSecret()
    {
        return config('getnet.'.config('app.env').'.client_secret');
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
        $this->createNumberToken();

        // if (!$this->getSuccess()) {
        //     return $this;
        // }

        $this->createCard();

        return $this;
    }

    /**
     * Create token number.
     *
     * @return $this
     */
    private function createNumberToken()
    {
        $url = $this->environment->getApiUrl().'v1/tokens/card';

        $auth = $this->auth->makeAuth();

        $client = new Client();

        try {
            $guzzleReturn = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => $auth->getTokenType().' '.$auth->getAccessToken(),
                ],
                'form_params' => [
                     'card_number' => $this->cardNumber,
                     'customer_id' => $this->customerId,
                ],
            ]);

            $return = json_decode($guzzleReturn->getBody(), true);

            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');
            $this->numberToken = $return['number_token'];
        } catch (RequestException $e) {
            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $e->getMessage());
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

        $url = $this->environment->getApiUrl().'v1/cards/verification';

        $auth = $card->auth;

        $client = new Client();

        try {
            $guzzleReturn = $client->request('POST', $url,
            [
                'headers' => [
                    'Authorization' => $auth->getTokenType().' '.$auth->getAccessToken(),
                    ],
                'form_params' => [
                    'number_token' => $this->numberToken,
                    'brand' => 'mastercard',
                    'cardholder_name' => 'JOAO DA SILVA',
                    'expiration_month' => '10',
                    'expiration_year' => '18',
                    'security_code' => '123',
                ],
            ]
        );

            $return = json_decode($guzzleReturn->getBody(), true);

            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');

            $this->verificationId = $return['verification_id'];
            $this->authorizationCode = $return['authorization_code'];

            return $this->payerAuthentication($auth->getTokenType(), $auth->getAccessToken());
        } catch (RequestException $e) {
            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $this->message = $e->getMessage());
        }
    }

    public function payerAuthentication($tokenType, $accessToken)
    {
        $headers = [
            'Authorization' => $tokenType.' '.$accessToken,
        ];

        $params = [
            'seller_id' => 'a0295238-a156-40ef-a3f8-f6d26b1e8934',
            'amount' => '1',
            'order' => json_encode(['order_id' => '6d2e4380-d8a3-4ccb-9138-c289182818a3', 'product_type' => 'service']),

            'customer' => json_encode([
              'customer_id' => '1',
              'name' => 'teste Silva',
              'billing_address' => '{
                "street": "Av. Brasil",
                "number": "1000",
                "complement": "Sala 1",
                "district": "São Geraldo",
                "city": "Porto Alegre",
                "state": "RS",
                "country": "Brasil",
                "postal_code": "90230060"
                }',
            ]),
            'debit' => json_encode([
              'delayed' => false,
              'save_card_data' => false,
              'transaction_type' => 'FULL',
              'number_installments' => 1,
              'card' => [
                'number_token' => $this->numberToken,
                'cardholder_name' => 'teste Silva',
                'expiration_month' => '12',
                'expiration_year' => '20',
              ],
            ]),
          ];

        $array['headers'] = $headers;
        $array['form_params'] = $params;

        $url = $this->environment->getApiUrl().'v1/payments/debit';
        $client = new Client();

        try {
            $guzzleReturn = $client->request('POST', $url, $array);

            $return = json_decode($guzzleReturn->getBody(), true);

            $this->payerAuthentication = $return['post_data']['payer_authentication_request'];
            $this->paymentId = $return['payment_id'];

            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');

            return $this->finishTransaction($tokenType, $accessToken, $this->paymentId, $this->payerAuthentication);
        } catch (RequestException $e) {
            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $this->message = $e->getMessage());
        }
    }

    public function finishTransaction($tokenType, $accessToken, $paymentId, $payerAuthentication)
    {
        $headers = [
            'Authorization' => $tokenType.' '.$accessToken,
        ];

        $params = [
            'payer_authentication_response' => $payerAuthentication,
          ];

        $array['headers'] = $headers;
        $array['form_params'] = $params;

        $url = $this->environment->getApiUrl().'v1/payments/debit/'.$paymentId.'/authenticated/finalize';
        $client = new Client();

        try {
            $guzzleReturn = $client->request('POST', $url, $array);

            $return = json_decode($guzzleReturn->getBody(), true);

            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');

            return $return;
        } catch (RequestException $e) {
            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $this->message = $e->getMessage());
        }
    }

    public function cancelDebitCardPayment($paymentId, $amount)
    {
        $card = $this->createNumberToken();
        $auth = $card->auth;

        $headers = [
          'Authorization' => $auth->getTokenType().' '.$auth->getAccessToken(),
        ];

        $params = [
                'payment_id' => $paymentId,
                'seller_id' => '8f840ea0-b7d1-42f5-ad78-5a5521e3125f',
                'amount' => 500000,
                'currency' => 'BRL',
                'order_id' => 'pre-auth-order-204',
                'status' => 'CANCELED',
                'credit_cancel' => [
                'canceled_at' => '2018-07-22T18:47:32.270Z',
                'message' => 'Credit transaction cancelled successfully',
                ],
          ];

        $array['headers'] = $headers;
        $array['form_params'] = $params;

        $url = $this->environment->getApiUrl().'v1/payments/credit/'.$paymentId.'/cancel';
        $client = new Client();

        try {
            $guzzleReturn = $client->request('POST', $url, $array);

            $return = json_decode($guzzleReturn->getBody(), true);

            $this->getnetResponse = new GetNetResponse(true, $guzzleReturn->getStatusCode(), '');

            return $return;
        } catch (RequestException $e) {
            $this->getnetResponse = new GetNetResponse(false, $e->getCode(), $this->message = $e->getMessage());
        }
    }
}
