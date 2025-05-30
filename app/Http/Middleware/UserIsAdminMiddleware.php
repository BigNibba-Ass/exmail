<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserIsAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(!auth()->user()->is_admin) abort(403);
        return $next($request);
    }
}
