<?php 
return [
    "active" => env("SHIPPING_MICRO_SERVICE", false),
    "endpoint" => [
        "getAuth"   => env("CALC_FRETE_GET_AUTH","http://local.padrao.fretecalc/api/auth/token"),
        "calculate" => env("CALC_FRETE_CALCULATE","http://local.padrao.fretecalc/api/shipping/calculate")
    ],
    "auth" =>  [
        "key"      =>   env("CALC_FRETE_KEY","5d3afb918220c"),
        "secret"   =>   env("CALC_FRETE_PASSWORD","p4dr4oc0l0rrrr"),
    ],
];