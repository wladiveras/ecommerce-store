<?php

namespace App\Models;

use marcusvbda\commonModels\Models\StatusOrderItem as _StatusOrderItem;

class StatusOrderItem extends _StatusOrderItem
{
  public $connection = "dashboard_server";
}
