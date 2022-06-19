<?php

namespace App\Models;

use marcusvbda\commonModels\Models\MeanPayment as _MeanPayment;

class MeanPayment extends _MeanPayment
{
  public $connection = "dashboard_server";
}
