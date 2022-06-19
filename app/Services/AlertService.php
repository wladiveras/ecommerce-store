<?php

namespace App\Services;

class AlertService
{
    public static function flash($alertType, $alertMessage, $closeable = true)
    {
        session()->flash('quick.alert', true);
        session()->flash('quick.alert.type', $alertType);
        session()->flash('quick.alert.message', $alertMessage);
        session()->flash('quick.alert.closeable', $closeable);

        return;
    }
}
