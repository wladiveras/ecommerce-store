<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\{CustomerAddress};

class Customer extends Model
{
	use SoftDeletes;

	public $connection = "dashboard_server";
    protected $table = 'customers';
    protected $fillable = [
		'name',
		'full_name',
		'email',
		'doc'
	];

	public function addresses()
	{
		return $this->hasMany(CustomerAddress::class);
	}

}
