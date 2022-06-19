<?php

namespace App\Models;

use marcusvbda\commonModels\Models\Product as _Product;
use App\Models\File;
use Illuminate\Database\Eloquent\Builder;

class Product extends _Product
{
    public $connection = "dashboard_server";

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('status', '>', 0);
        });
    }

    public function getBasePriceAttribute($value)
    {
        if (\Auth::user()) {
            return @$this->minPrice->min_price;
        } else {
            return null;
        }
    }

    public function skus()
    {
        return $this->hasMany(Sku::class);
    }

    public function files($ref = ['image'])
    {
        $model = $this->getMorphClass();
        $model = strtolower(class_basename($model));
        $relation = $this->belongsToMany(File::class, '_files_relation', 'model_id', 'file_ref', null, 'ref');
        $relation = $relation->where('model_type', $model)->whereIn('_files_relation.ref', $ref)->orderBy('ordination');
        return $relation;
    }
}
