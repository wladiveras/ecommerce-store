<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use App\Models\Business\Business_Product;

class Business_ extends Model
{
    
    public $connection = "dashboard_server";
    protected $table = "business_";
    public $files;

    protected $appends = ['files'];

    public function getFilesAttribute()
    {
        return $this->files;
    }
    
    public function business_product()
    {
        return $this->hasMany(Business_Product::class);
    }
}
