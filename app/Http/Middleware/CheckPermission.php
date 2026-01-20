<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Super Admin & Manager bypass for everything
        if ($request->user() && ($request->user()->hasRole('super-admin') || $request->user()->hasRole('manager'))) {
             return $next($request);
        }

        if (! $request->user() || ! $request->user()->can($permission)) {
             // Or use our custom method if not using Gate: $request->user()->permissions->contains('slug', $permission)
             // But let's assume we map permissions to Gates or check directly.
             // Given our User model `permissions()` relation, we can check:
             
             $hasPermission = $request->user()->permissions->contains('slug', $permission) || 
                              $request->user()->roles->flatMap->permissions->contains('slug', $permission);

             if (! $hasPermission) {
                abort(403, 'Unauthorized. Missing permission: ' . $permission);
             }
        }

        return $next($request);
    }
}
