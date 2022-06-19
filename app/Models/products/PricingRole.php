<?php

namespace App\Models;
use marcusvbda\commonModels\Models\PricingRole as _PricingRole;


class PricingRole extends _PricingRole
{
  public $connection = "dashboard_server";
/*
    public static function boot()
  {
    parent::boot();

    static::created(function ($model) {
      if ($model->connection == 'dashboard_server') {
        $data = $model->only($model->getFillable());
        PricingRole::on('ecommerce_server')->create($data);
      }
    });

    static::updated(function ($model) {
      if ($model->connection == 'dashboard_server') {
        $data = $model->attributes;
        PricingRole::on('ecommerce_server')->where('id', $model->id)->update($data);
      }
    });

    static::deleted(function ($model) {
      Sku::where('id', $model->id)->delete();
      if ($model->connection == 'dashboard_server') {
        PricingRole::on('ecommerce_server')->where('id', $model->id)->delete();
      }
    });
  }
  */

}
