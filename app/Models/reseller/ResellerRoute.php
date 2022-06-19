<?php

namespace App\Models;

use marcusvbda\commonModels\Models\ResellerRoute as _ResellerRoute;
use Illuminate\Database\Eloquent\Model;
class ResellerRoute extends _ResellerRoute
{
  public $guarded = ['id','created_at','updated_at'];
  public $casts = [
    'info' => 'array'
  ];
}
