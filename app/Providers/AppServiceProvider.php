<?php

namespace App\Providers;

use Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
  * Bootstrap any application services.
  *
  * @return void
  */
  public function boot()
  {
   
    \Blade::directive('svg', function($arguments) {
      // Funky madness to accept multiple arguments into the directive
      list($path, $class) = array_pad(explode(',', trim($arguments, "() ")), 2, '');
      $path = trim($path, "' ");
      $class = trim($class, "' ");

      // Create the dom document as per the other answers
      $svg = new \DOMDocument();
      $svg->load(public_path($path));
      $svg->documentElement->setAttribute("class", $class);
      $defs = $svg->getElementsByTagName('defs')->item(0);
      if($defs){
        $defs->parentNode->removeChild($defs);
      }
      $output = $svg->saveXML($svg->documentElement);

      return $output;
    });
  }

  /**
  * Register any application services.
  *
  * @return void
  */
  public function register()
  {
    //
  }
}
