<?php

/**
 * Copyright (c) 2019 - present
 * author: Franklin Sales - ecommerce@padraocolor.com.br
 * Initial version created on: 17/10/2019
 */

return [
    /**
     *
     * The payment switch to choose what service gateway to use
     *
     */
    'payment_service' => env('PAYMENT_SERVICE', 'cielo'),
];
