<?php

namespace ismaelgr\getnet;

use ismaelgr\getnet\services\Payment;
use ismaelgr\getnet\request\GetNetResponse;
use GuzzleHttp\Exception\RequestException;

class GetnetClass
{
    public static function makeTransaction($Request)
    {

        $payment = new Payment();
        try {
            
            $transaction = $payment->makePayment($Request);
            
        } catch (RequestException $e) {
          
            return new GetNetResponse(false, $e->getCode(), $e->getMessage());
        }
        return $transaction;    
    }
}
