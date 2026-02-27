<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Role;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = $request->user();
        if (!$user || !$user->role) {
            abort(403);
        }

        $role = Role::where('slug', $user->role)->first();
        if (!$role) {
            abort(403);
        }

        $perms = $role->permissions ?? [];
        if ($perms === ['*']) {
            return $next($request);
        }

        foreach ($permissions as $perm) {
            if (in_array($perm, $perms, true)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
