<?php

/**
 * Copyright (c) 2019 - present
 * author: Franklin Sales - ecommerce@padraocolor.com.br
 * Initial version created on: 17/10/2019
 */

return [
    /**
     *
     * The cielo credit card switch to choose what type credit card payment do you want to use.
     *
     */
    'cielo_creditcard' => env('CIELO_CREDITCARD', 'full'),
    'cielo_url' => env("CIELO_URL", ""),
    'cielo_endpoint_venda' => env("CIELO_ENDPOINT_VENDA",""),
    'cielo_url_consulta' => env("CIELO_URL_CONSULTA", ""),
    'cielo_endpoint_consulta_cartao' => env("CIELO_ENDPOINT_CONSULTA_CARTAO",""),
    'cielo_merchantId' => env("CIELO_MERCHANTID", ""),
    'cielo_merchantKey' => env("CIELO_MERCHANTKEY", ""),
    'cielo_api_url_padrao_color' => env("CIELO_PADRAO_COLOR_URL", ""),
];
