<?php

namespace App\Models;

use marcusvbda\commonModels\Models\Order as _Order;
use App\Models\Scopes\UserScope;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use \OwenIt\Auditing\Auditable;
class Order extends _Order implements AuditableContract
{
    use Auditable;
    public $connection = "dashboard_server";
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new UserScope());
    }

    public function getPaymentPendingAttribute()
    {
        $payments = $this->payments;
        $approved = StatusOrderPayment::where("value","pending")->first();
        return ($payments->last()->status_id == $approved->id) ;
    }

    public function getEstimatedShippingDateAttribute(){
        $base = parent::getEstimatedShippingDateAttribute();
        $wasClosed = (int) isFactoryClosed($this->created_at);
        if($wasClosed && $base["raw"]){
            $base["raw"]->addWeekdays(1);
            $base["formated"] = $base["raw"]->format("d/m/Y");
        }
        return $base;
    }
   
}
