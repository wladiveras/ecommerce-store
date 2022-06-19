<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Models\Business\Business_;
use App\Models\Product;

class Business_Product extends Model
{
    protected $table = 'business_product';
    public $connection = "dashboard_server";
    
    public function business()
    {
        return $this->belongsTo(Business_::class,"businessid","id");
    }

    public function sku()
    {
        return $this->belongsTo(Sku::class,"sku_id");
    }

    public function products()
    {
        return $this->belongsTo(Product::class,"product_id");
    }
}
