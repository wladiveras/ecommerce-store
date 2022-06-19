<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class checkTicketsAgent
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
        if(!Auth::check())
            abort(404);
        if(!Auth::user()->ticketit_agent)
            abort(403);
        return $next($request);
    }
}
