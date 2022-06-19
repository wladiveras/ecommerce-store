<?php 
return [
    "order_cancel" => [
        "enabled" => env("ENABLE_ORDER_CANCEL") ? true : false,
        "method"  => "DELETE",
        "route"   => env("DASHBOARD_ROUTE") . "/api/order"
    ],

    "resend_art" => [
        "enabled" => env("ENABLE_RESEND_ART", false),         
        "method" => "POST",
        "route"  => env('DASHBOARD_ROUTE') . "/api/order/resendart"
    ],

    "new_order_email" => [
        "enabled" => true,
        "method" => "POST",
        "route"  => env('DASHBOARD_ROUTE') . "/api/order"
    ],

    "new_special_order_email" => [
        "enabled" => true,
        "method" => "POST",
        "route"  => env('DASHBOARD_ROUTE') . "/api/special-order"
    ],

    "reset_password" => [
        "enabled" => true,
        "method" => "POST",
        "route"  => env('DASHBOARD_ROUTE') . "/api/reset-password"
    ]
];