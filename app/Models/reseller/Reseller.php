<?php

namespace App\Models;

use marcusvbda\commonModels\Models\Reseller as _Reseller;

class Reseller extends _Reseller
{
  public $connection = "dashboard_server";
  public $casts = [
    'full_address' => 'json'
  ];

  public $append = [
    "logoCdnUrl"
  ];

  public function getLogoCdnUrlAttribute($value)
  {
    return config("filesystems.disks.cdn.api_url").'/'.$this->logo_url;
  }

  public function setPhoneAttribute($value)
  {
    $this->attributes['phone'] = preg_replace('/[^A-Za-z0-9]/', '', $value);
  }

  public function getPhoneAttribute($value)
  {
    return $this->formatPhone($value);
  }
  private function formatPhone($value)
  {
    switch ($value) {
      case strlen($value) < 11:
        $value = '(' . substr($value, 0, 2) . ') ' . substr($value, 2, 4) . '-' . substr($value, 6);
        break;
      case strlen($value) > 10:
        $value = '(' . substr($value, 0, 2) . ') ' . substr($value, 2, 5) . '-' . substr($value, 7);
        break;
    }
    return $value;
  }
  public function getDocType()
  {
    if ($this->type == 0) {
      return "CPF";
    } else {
      return "CNPJ";
    }
  }

  public function route()
  {
    return $this->belongsTo("App\Models\ResellerRoute", 'reseller_route_id', 'id');
  }
}
