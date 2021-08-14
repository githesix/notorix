<?php

namespace App\Http\Middleware;

use Closure;

class CheckGroupe
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $groupe)
    {
        if (!($request->user()->isMemberOf($groupe))) {
            abort(403, __('Access denied'));
        }
        return $next($request);
    }
}