<?php
namespace App\Services\BusinessRule;
use App\Models\StatusOrder;
use Carbon;

class OrderTimeline 
{
    private $order   = null;
    private $audits  = null;
    private $changes = [];
    private $actual  = "waiting";
    private $steps   = [
        "waiting" => [
            "status" => null,
            "date"   => "...",
            "time"   => "..."
        ],
        "processing" => [
            "status" => null,
            "date"   => "...",
            "time"   => "..."
        ],
        "production" => [
            "status" => null,
            "date"   => "...",
            "time"   => "..."
        ],
        "requested_invoice" => [
            "status" => null,
            "date"   => "...",
            "time"   => "..."
        ],
        "shipping" => [
            "status" => null,
            "date"   => "...",
            "time"   => "..."
        ],
        "delivering_error" => [
            "status" => null,
            "date"   => "...",
            "time"   => "..."
        ],
        "delivered" => [
            "status" => null,
            "date"   => "...",
            "time"   => "..."
        ]
    ];

    public function __construct($order) 
    {
        $this->order = $order;
        return $this;
    }

    public function getOrderTimeline()
    {
        if($this->order->status->value=="canceled") return "canceled";
        $this->audits = $this->getStatusChange();
        $this->correctSteps();
        return $this->steps;
    }

    private function correctSteps()
    {
        foreach($this->steps as $key=>$value)
        {
            if($this->steps[$key]["date"]=="...")
                $this->correct($key);
        }
        $this->steps[$this->actual]["status"] = $this->actual=="delivered" ? "selected" : "actual";
    }

    private function correct($key)
    {
        $status = $this->order->status;
        switch($key)
        {
            case "waiting" :
                $this->getWaitingStep();
            break;
            case "processing" :
                if(in_array($status->value,["approved","production","finishing","forwarded","finished","on_shipment"]))
                    $this->getProcessingStep();
            break;
            case "production" :
                if(in_array($status->value,["production","finishing","forwarded","finished","on_shipment"]))
                    $this->getProductionStep();
            break;
            case "requested_invoice" :
                if(in_array($status->value,["finishing","on_shipment", "forwarded", "finished"]))
                    $this->getRequestedInvoice();
            break;
            case "shipping" :
                if(in_array($status->value,["forwarded", "finished"]))
                    $this->getShippingStep();
            break;
            case "delivering_error" :
                if(in_array($status->value,["delivering_error", "finished"]))
                    $this->getDeliveringError();
            break;
            case "delivered" :
                if(in_array($status->value,["finishing","finished"]))
                    $this->getDeliveredStep();
            break;
        }
    }

    private function getDeliveredStep()
    {
        $datetime = Carbon::make("0001-01-01");
        foreach($this->changes as $change)
        {
            if($change["status"]->value=="finished")
            {
                if($datetime<$change["datetime"]) 
                    $datetime = $change["datetime"];
            }
        }
        if($datetime!=Carbon::make("0001-01-01")) 
        {
            $this->steps["delivered"]["date"]   = $datetime->format("d/m/Y");
            $this->steps["delivered"]["time"]   = $datetime->format("H:i:s");
            $this->steps["delivered"]["status"] = "selected";
            $this->actual = "delivered";
        } else {
            $this->steps["delivered"]["date"]   = "Sem Data";
            $this->steps["delivered"]["time"]   = "Sem Hora";
            $this->steps["delivered"]["status"] = "selected";
            $this->actual = "delivered";   
        }
    }

    private function getShippingStep()
    {
        $datetime = Carbon::make("0001-01-01");
        foreach($this->changes as $change)
        {
            if($change["status"]->value=="forwarded")
            {
                if($datetime<$change["datetime"]) 
                    $datetime = $change["datetime"];
            }
        }
        if($datetime!=Carbon::make("0001-01-01")) 
        {
            $this->steps["shipping"]["date"]   = $datetime->format("d/m/Y");
            $this->steps["shipping"]["time"]   = $datetime->format("H:i:s");
            $this->steps["shipping"]["status"] = "selected";
            $this->actual = "shipping";
        } else {
            $this->steps["shipping"]["date"]   = "Sem Data";
            $this->steps["shipping"]["time"]   = "Sem Hora";
            $this->steps["shipping"]["status"] = "selected";
            $this->actual = "shipping";   
        }
    }
    
    private function getProductionStep()
    {
        $datetime = Carbon::make("0001-01-01");
        foreach($this->changes as $change)
        {
            if($change["status"]->value=="pending")
            {
                if($datetime<$change["datetime"]) 
                    $datetime = $change["datetime"];
            }
        }
        if($datetime!=Carbon::make("0001-01-01"))  
        {
            $this->steps["production"]["date"]   = $datetime->format("d/m/Y");
            $this->steps["production"]["time"]   = $datetime->format("H:i:s");
            $this->steps["production"]["status"] = "selected";
            $this->actual = "production";
        } else {
            $this->steps["production"]["date"]   = "Sem Data";
            $this->steps["production"]["time"]   = "Sem Hora";
            $this->steps["production"]["status"] = "selected";
            $this->actual = "production";            
        }
    }

    private function getRequestedInvoice()
    {
        $datetime = Carbon::make("0001-01-01");
        foreach($this->changes as $change)
        {
            if($change["status"]->value=="on_shipment")
            {
                if($datetime<$change["datetime"]) 
                    $datetime = $change["datetime"];
            }
        }
        if($datetime!=Carbon::make("0001-01-01"))  
        {
            $this->steps["requested_invoice"]["date"]   = $datetime->format("d/m/Y");
            $this->steps["requested_invoice"]["time"]   = $datetime->format("H:i:s");
            $this->steps["requested_invoice"]["status"] = "selected";
            $this->actual = "requested_invoice";
        } else {
            $this->steps["requested_invoice"]["date"]   = "Sem Data";
            $this->steps["requested_invoice"]["time"]   = "Sem Hora";
            $this->steps["requested_invoice"]["status"] = "selected";
            $this->actual = "requested_invoice";            
        }
    }

    private function getDeliveringError()
    {
        $datetime = Carbon::make("0001-01-01");
        foreach($this->changes as $change)
        {
            if($change["status"]->value=="delivering_error")
            {
                if($datetime<$change["datetime"]) 
                    $datetime = $change["datetime"];
            }
        }
        if($datetime!=Carbon::make("0001-01-01"))  
        {
            $this->steps["delivering_error"]["date"]   = $datetime->format("d/m/Y");
            $this->steps["delivering_error"]["time"]   = $datetime->format("H:i:s");
            $this->steps["delivering_error"]["status"] = "selected";
            $this->actual = "delivering_error";
        } else {
            $this->steps["delivering_error"]["date"]   = "Sem Data";
            $this->steps["delivering_error"]["time"]   = "Sem Hora";
            $this->steps["delivering_error"]["status"] = "selected";
            $this->actual = "delivering_error";            
        }
    }


    private function getProcessingStep()
    {   
        $datetime = Carbon::make("0001-01-01");
        foreach($this->changes as $change)
        {
            if($change["status"]->value=="approved")
            {
                if($datetime<$change["datetime"]) 
                    $datetime = $change["datetime"];
            }
        }
        if($datetime!=Carbon::make("0001-01-01"))  
        {
            $this->steps["processing"]["date"]   = $datetime->format("d/m/Y");
            $this->steps["processing"]["time"]   = $datetime->format("H:i:s");
            $this->steps["processing"]["status"] = "selected";
            $this->actual = "processing";
        } else {
            $this->steps["processing"]["date"]   = "Sem Data";
            $this->steps["processing"]["time"]   = "Sem Hora";
            $this->steps["processing"]["status"] = "selected";
            $this->actual = "processing";            
        }
    }

    private function getWaitingStep()
    {
        $this->steps["waiting"]["date"]   = $this->order->created_at->format("d/m/Y");
        $this->steps["waiting"]["time"]   = $this->order->created_at->format("H:i:s");
        $this->steps["waiting"]["status"] = "selected";
    }

    private function getStatusChange()
    {
        $changes = [];
        $audits = clone $this->order->audits()->orderBy("created_at","asc")->get();
        foreach($audits as $audit)
        {
            $old_status = @$audit->old_values["status_id"];
            $new_status = @$audit->new_values["status_id"];
            if($new_status) 
            {
                if($new_status!=$old_status)
                {
                    $changes[] = [
                        "datetime"   => $audit->created_at,
                        "status" => StatusOrder::findOrFail($new_status)
                    ];
                }
            }
        }
        $this->changes = $changes;
        return $this;
    }
}