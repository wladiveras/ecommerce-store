<?php

namespace App\Http\Middleware;

use Closure;

class DisplayTitle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$title)
    {
        \Illuminate\Support\Facades\View::share('title', $title);
        return $next($request);
    }
}
