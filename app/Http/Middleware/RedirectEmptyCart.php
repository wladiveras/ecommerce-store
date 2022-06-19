<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\AlertService;
class RedirectEmptyCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = \Auth::user();
        $cart = \Cart::session($user->code);
        if($cart->isEmpty()){
          AlertService::flash('warning',"Seu carrinho está vazio. Para prosseguir você precisa adicionar pelo menos um item.");
          return back();
        }else{
          return $next($request);
        }
    }
}
