<?php

namespace App\Models;

use marcusvbda\commonModels\Models\StatusOrder as _StatusOrder;

class StatusOrder extends _StatusOrder
{
  public $connection = "dashboard_server";
}
