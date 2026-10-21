<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdvisorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->user()?->role;

        if ($role !== 'advisor' && $role !== 'admin') {
            abort(403);
        }

        return $next($request);
    }
}
