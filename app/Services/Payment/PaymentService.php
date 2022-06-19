<?php

namespace App\Services\Payment;

use App\Services\Payment\GetNetPayment;
use App\Services\Payment\CieloPayment;

class PaymentService
{

    public function paymentRouter($order, $data)
    {
        try {
            $paymentService = config('payment.payment_service');

            $returnPayment = null;
            if(isset($data["payment"]["type"])){
                if($data["payment"]["type"] == "bankslip" || $data["payment"]["type"] == "paylater"){
                    $paymentService = "getnet";
                }
            }
            switch ($paymentService) {
                case 'getnet':
                    $getNetPayment = new GetNetPayment();
                    $returnPayment = $getNetPayment->payment($order, $data);
                    break;

                case 'cielo':
                    $cieloPayment = new CieloPayment();
                    $returnPayment = $cieloPayment->payment($order, $data);
                    return $returnPayment;
                    break;

                default:
                    throw new \Exception("Payment Service unknown");

                    break;
            }

            return $returnPayment;
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
        }
    }
}
