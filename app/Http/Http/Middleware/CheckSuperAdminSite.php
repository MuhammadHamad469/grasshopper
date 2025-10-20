<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSuperAdminSite
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('app.is_super_admin_site')) {
            // Return a 403 Forbidden with message
            abort(403, 'Permission denied.');
        }

        return $next($request);
    }
}
