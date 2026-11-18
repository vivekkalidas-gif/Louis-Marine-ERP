<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts the application to login + certificate management when
 * config('app.certificate_only') is true (CERTIFICATE_ONLY=true in .env).
 *
 * Routes stay registered so views that build links with route() keep
 * rendering; requests to anything outside the allowlist are refused.
 */
class CertificateOnlyMode
{
    /** Route-name patterns that remain reachable. */
    public const ALLOWED = [
        // Authentication (no self-registration)
        'login', 'logout',
        'password.*', 'verification.*',
        'profile.*',

        // Certificate management
        'admin.certificates.*',
        'admin.certificate-templates.*',
        'admin.template-sections.*',
        'admin.template-fields.*',
        'certificates.verify-public',

        // Data certificates depend on
        'admin.equipment.*',
        'admin.customers.*',
        'admin.vessels.*',

        // Creating login accounts and company details shown on certificates
        'admin.users.*',
        'admin.settings.*',
    ];

    /** Unnamed paths that remain reachable. */
    private const ALLOWED_PATHS = [
        // Unnamed POST routes of the auth flow (e.g. the login form submit)
        'login',
        'confirm-password',
        'storage-file/*',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.certificate_only')) {
            return $next($request);
        }

        $name = $request->route()?->getName();

        // Landing points go straight to the certificate manager.
        if ($request->is('/') || in_array($name, ['home', 'dashboard'], true)) {
            return auth()->check()
                ? redirect()->route('admin.certificates.index')
                : redirect()->route('login');
        }

        if ($name !== null && $request->routeIs(...self::ALLOWED)) {
            return $next($request);
        }

        if ($name === null && $request->is(...self::ALLOWED_PATHS)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Not available.'], 404);
        }

        return auth()->check()
            ? redirect()->route('admin.certificates.index')->with('error', 'That section is not available.')
            : redirect()->route('login');
    }
}
