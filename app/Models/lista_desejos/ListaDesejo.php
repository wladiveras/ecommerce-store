<?php

namespace App\Models\lista_desejos;

use Illuminate\Database\Eloquent\Model;

class ListaDesejo extends Model
{
    
    public $connection = "dashboard_server";
    protected $table = "lista_desejo";
    
    protected $fillable = [
        'id_user',
    ];

    protected $cart = [
        'properties' => 'array'
    ];
}