<?php

namespace App\Models;

use marcusvbda\commonModels\Models\StatusOrderPayment as _StatusOrderPayment;

class StatusOrderPayment extends _StatusOrderPayment
{
  public $connection = "dashboard_server";
}
