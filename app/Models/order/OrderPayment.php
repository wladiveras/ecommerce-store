<?php

namespace App\Models;

use marcusvbda\commonModels\Models\OrderPayment as _OrderPayment;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;
class OrderPayment extends _OrderPayment implements AuditableContract
{
	use Auditable;
  public $connection = "dashboard_server";
  public $fillable = [
    'status_id','order_id','gateway','payment_id','payment_date','data','amount', 'log'
  ];

  public function getDataAttribute($value){
    $value = json_decode($value,true);
    return array_except($value,'response');
  }
    // public static function boot()
    // {
    //     parent::boot();

    //     static::created(function ($model) {
    //         if ($model->connection == 'mysql') {
    //             $data = $model->attributes;
    //             _OrderPayment::on('dashboard_server')->create($data);
    //         }
    //     });

    //     static::updated(function ($model) {
    //         if ($model->connection == 'mysql') {
    //             _OrderPayment::on('dashboard_server')->where('id', $model->id)->update($data);
    //         }
    //     });
    // }
}
