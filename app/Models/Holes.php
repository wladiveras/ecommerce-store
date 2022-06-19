<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holes extends Model
{
    public $guarded = ['created_at'];
    public $connection = "dashboard_server";
    protected $table = 'model_has_roles';
}
