<?php

namespace App\Models;

use marcusvbda\commonModels\Models\Sku as _Sku;

class Sku extends _Sku
{
    public $connection = "dashboard_server";
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function attributes_list()
    {
        $variations = $this->product->variations;
        $attributes = $this->getAttribute('attributes');
        $res = [];
        foreach ($variations as $key => $variation) {
            $res[$variation] = $attributes[$key];
        }
        return $res;
    }
    public function finishes()
    {
        return $this->hasMany(Finish::class);
    }
    public function has_measures(){
        return preg_match("/M\\\u00b2/",$this->getOriginal('attributes'));
    }

}
