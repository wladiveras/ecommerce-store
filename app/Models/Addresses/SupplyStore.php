<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SupplyStore extends Model
{
    public $connection = "dashboard_server";
    public $guarded = ['id', 'created_at', 'updated_at'];
    public $hidden = ['street', 'number', 'complement', 'reference', 'district', 'city', 'state', 'zip_code', 'created_at', 'updated_at'];
    protected $casts = [
        'phone' => 'json',
        'data' => 'array'
    ];
    public $appends = ['address', 'shipping_delay'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('addressType', function (Builder $builder) {
            return $builder->where('type', 'supply_store');
        });
    }

    public function setStreetAttribute($value)
    {
        $res = explode(',', $value);
        $this->attributes['street'] = $res[0];
        if (count($res) > 1) {
            $this->attributes['number'] = $res[1];
        }
    }
    public function getAddressAttribute()
    {
        $data = $this->getAttributes();
        $only = $this->hidden;
        return array_only($data, $only);
    }

    public function getShippingDelayAttribute()
    {
        $delay = @$this['data']['shipping_delay'];
        return (int) $delay;
    }
}
