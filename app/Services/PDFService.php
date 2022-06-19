<?php
namespace App\Services;

class PDFService {
  public static function stream($view, $data = [], $name){
    $pdf = \PDF::loadView($view, $data);
    return $pdf->stream($name);
  }
}
