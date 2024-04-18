<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\URL;

class ForceHttpsMiddleware
{
    protected $middlewareKey = 'force.https';
    public function handle($request, Closure $next)
    {
        URL::forceScheme('https');

        return $next($request);
    }
}
