<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use marcusvbda\commonModels\Models\OrderSku as _OrderSku;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;
class OrderSku extends _OrderSku  implements AuditableContract
{
  use Auditable;
  public $connection = "dashboard_server";
    // public static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($model) {
    //         if ($model->connection == 'mysql') {
    //             $data = $model->attributes;
    //             _OrderSku::on('dashboard_server')->create($data);
    //         }
    //     });

    //     static::updated(function ($model) {
    //         if ($model->connection == 'mysql') {
    //             _OrderSku::on('dashboard_server')->where('id', $model->id)->update($data);
    //         }
    //     });
    // }
}
