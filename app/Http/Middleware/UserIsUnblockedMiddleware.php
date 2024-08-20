<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserIsUnblockedMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_blocked) abort(403);
        return $next($request);
    }
}
