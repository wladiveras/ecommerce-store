<?php

namespace App\Http\Middleware;

use Log;
use Closure;

class SlugIds
{
    public function handle($request, Closure $next, ...$ids)
    {
        try {
            $value = app($ids[1])::findBySlug($request->route($ids[0]));
            $request->route()->setParameter($ids[0], $value);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Índice não encontrado.',
                ]);
            }

            Log::warning('Invalid ID. Method: '.$request->route()->action['as']);

            return abort(404);
        }

        return $next($request);
    }
}
