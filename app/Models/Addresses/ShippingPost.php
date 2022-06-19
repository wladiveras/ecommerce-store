<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ShippingPost extends Model
{
  public $connection = 'dashboard_server';
  public $table = "supply_stores";
  public $guarded = ['id', 'created_at', 'updated_at'];
  public $hidden = ['street','number','complement','reference','district','city','state','zip_code'];
  protected $casts = [
    'phone' => 'array',
    'data' => 'array'
  ];
  protected static function boot()
  {
    parent::boot();

    static::addGlobalScope('type', function (Builder $builder) {
      $builder->where('type', 'shipping_post');
    });
  }
  //public $appends = ['address'];
  public function setStreetAttribute($value)
  {
    $res = explode(',', $value);
    $this->attributes['street'] = $res[0];
    if (count($res) > 1) {
      $this->attributes['number'] = $res[1];
    }
  }
  public function getPhoneAttribute($value)
  {
    return (Array)json_decode($value);
  }
  
  public function getAddressAttribute(){
    $data = $this->getAttributes();
    $only = $this->hidden;
    return array_only($data,$only);
  }
}
