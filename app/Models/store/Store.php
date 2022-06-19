<?php

namespace App\Models;

use marcusvbda\commonModels\Models\Store as _Store;

class Store extends _Store
{
  public $connection = 'dashboard_server';
}
