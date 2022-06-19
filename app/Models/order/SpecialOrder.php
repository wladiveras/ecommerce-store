<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use marcusvbda\commonModels\Models\SpecialOrder as _SpecialOrder;
use Auth;

class SpecialOrder extends _SpecialOrder
{
    public $connection = "dashboard_server";
    protected static function boot()
    {
        parent::boot();
        if (!@Auth::user()->ticketit_agent) {
            static::addGlobalScope(new UserScope());
        }
    }
}
