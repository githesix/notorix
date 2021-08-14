<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    // use \App\Http\Traits\ClefierTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $niveau)
    {
        if (!($request->user()->role & $niveau)) {
            abort(403, __('Access denied'));
        }
        return $next($request);
    }
}