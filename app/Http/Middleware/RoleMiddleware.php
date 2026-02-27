<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // Super admin bypasses role checks
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        if (!in_array($user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
