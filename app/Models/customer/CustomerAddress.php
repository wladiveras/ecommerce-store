<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\{Customer};

class CustomerAddress extends Model
{
	use SoftDeletes;

		public $connection = "dashboard_server";
    protected $table = 'customer_addresses';
    protected $fillable = [
		'type',
		'zip_code',
		'location',
		'address',
		'number',
		'complement',
		'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
