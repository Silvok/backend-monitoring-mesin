<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Cache;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = $request->user();
        if (!$user || !$user->role) {
            abort(403);
        }

        $cacheKey = 'role_permissions:' . $user->role;
        $perms = Cache::remember($cacheKey, 300, function () use ($user) {
            $role = Role::query()->select(['id', 'permissions'])->where('slug', $user->role)->first();
            return $role?->permissions;
        });

        if (!$perms) {
            abort(403);
        }

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
