<?php

namespace App\Models;

use marcusvbda\commonModels\Models\PricingRules as _PricingRules;

class PricingRules extends _PricingRules
{
    public $connection = "dashboard_server";
}
