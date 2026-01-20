<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (! $request->user()) {
            abort(403, 'Unauthorized');
        }

        $roles = is_array($role)
            ? $role
            : explode('|', $role);

        if (! $request->user()->hasRole($roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
