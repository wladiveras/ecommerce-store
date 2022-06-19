<?php

namespace ismaelgr\getnet\services;

use Getnet\Getnet\Auth;
use ismaelgr\getnet\config\Environment;

/**
 * Class Payment.
 */
class Payment
{
    /** @var Environment $environment */
    private $environment;

    /** @var Auth $auth */
    private $auth;

    /** @var bool $success */
    private $success;

    /** @var string $code */
    private $code;

    /** @var string $message */
    private $message;

    public function __construct()
    {
        $this->environment = new Environment();
    }

    /**
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function makePayment($request)
    {  
        switch ($request['action']) {
            case 0:
                $creditCard = new CreditCard($this->environment, $request);
                $transaction = $creditCard->tokenizeCard();
            break;

            case 1:
                $debitCard = new DebitCard($this->environment, $request);
                $transaction = $debitCard->tokenizeCard();
            break;

            case 2:
                $bankSlip = new BankSlip($this->environment, $request);
                $transaction = $bankSlip->registerPayment();
            break;

            case 3:
                $creditCard = new CreditCard($this->environment, $request);
                $transaction = $creditCard->cancelCredCardPayment();
            break;

            case 4:
                $creditCard = new CreditCard($this->environment, $request);
                $transaction = $creditCard->saveCreditCard();
            break;

            case 5:
                $cardAction = $request['cardAction']; 
                $cardId = $request['cardId']; 
                
                $creditCard = new CreditCard($this->environment, $request);
                $transaction = $creditCard->safeBox($cardAction,$cardId);
            break;

            
            case 6:
                $costumerId = $request['customerId'];
                $creditCard = new CreditCard($this->environment, $request);
                $transaction = $creditCard->safeBoxListAll($costumerId);
            break;
        }
        return $transaction;
    }
}
