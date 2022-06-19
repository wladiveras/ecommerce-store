<?php

namespace App\Models;

use marcusvbda\commonModels\Models\Finish as _Finish;


class Finish extends _Finish
{
    public $connection = "dashboard_server";
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }

    /*
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            if ($model->connection == 'dashboard_server') {
                $data = $model->attributes;
                Finish::on('ecommerce_server')->create($data);
            }
        });
        static::updating(function ($model) {
            if ($model->connection == 'dashboard_server') {
                $data = $model->attributes;
                Finish::on('ecommerce_server')->where('id', $model->id)->update($data);
            }
        });

        static::deleted(function ($model) {
            if ($model->connection == 'dashboard_server') {
                Finish::on('ecommerce_server')->where('id', $model->id)->delete();
            }
        });
    }
    */
}
