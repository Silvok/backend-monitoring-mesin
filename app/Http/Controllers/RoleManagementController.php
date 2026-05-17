<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RoleManagementController extends Controller
{
    private function hasPermission(Request $request, string $permission): bool
    {
        $user = $request->user();
        if (!$user) {
            return false;
        }

        // Super admin always allowed.
        if ($user->role === 'super_admin') {
            return true;
        }

        if (!$user->role) {
            return false;
        }

        $cacheKey = 'role_permissions:' . $user->role;
        $perms = Cache::remember($cacheKey, 300, function () use ($user) {
            $role = Role::query()
                ->select(['id', 'permissions'])
                ->where('slug', $user->role)
                ->first();

            return $role?->permissions;
        });

        if (!$perms) {
            return false;
        }

        if ($perms === ['*']) {
            return true;
        }

        return in_array($permission, $perms, true);
    }

    private function isSystemProtected(Role $role): bool
    {
        return $role->slug === 'super_admin';
    }

    public function index(Request $request)
    {
        if (!$this->hasPermission($request, 'roles.view')) {
            abort(403);
        }

        $canCreateRole = $this->hasPermission($request, 'roles.create');
        $canEditRole = $this->hasPermission($request, 'roles.edit');
        $canDeleteRole = $this->hasPermission($request, 'roles.delete');

        $roles = Role::orderBy('is_protected', 'desc')
            ->orderBy('name')
            ->get();

        $permissionGroups = config('permissions');
        $permissionLabels = [];
        foreach ($permissionGroups as $group => $perms) {
            foreach ($perms as $key => $label) {
                $permissionLabels[$key] = $label;
            }
        }

        return view('pages.role-management', compact(
            'roles',
            'permissionGroups',
            'permissionLabels',
            'canCreateRole',
            'canEditRole',
            'canDeleteRole'
        ));
    }

    public function store(Request $request)
    {
        if (!$this->hasPermission($request, 'roles.create')) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses create role.'], 403);
        }

        $data = $this->validateRole($request);
        $data['slug'] = $this->slugify($data['slug'] ?? $data['name']);
        $data['permissions'] = $data['permissions'] ?? [];
        $data['is_protected'] = false;

        if (Role::where('slug', $data['slug'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Slug role sudah digunakan.'], 422);
        }

        $role = Role::create($data);
        return response()->json(['success' => true, 'role' => $role]);
    }

    public function update(Request $request, Role $role)
    {
        if (!$this->hasPermission($request, 'roles.edit')) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses edit role.'], 403);
        }

        if ($this->isSystemProtected($role)) {
            return response()->json(['success' => false, 'message' => 'Role ini dilindungi.'], 403);
        }

        $data = $this->validateRole($request);
        $data['slug'] = $this->slugify($data['slug'] ?? $data['name']);
        $data['permissions'] = $data['permissions'] ?? [];

        $exists = Role::where('slug', $data['slug'])
            ->where('id', '!=', $role->id)
            ->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Slug role sudah digunakan.'], 422);
        }

        $role->update($data);
        return response()->json(['success' => true, 'role' => $role]);
    }

    public function destroy(Request $request, Role $role)
    {
        if (!$this->hasPermission($request, 'roles.delete')) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses delete role.'], 403);
        }

        if ($this->isSystemProtected($role)) {
            return response()->json(['success' => false, 'message' => 'Role ini dilindungi.'], 403);
        }

        $role->delete();
        return response()->json(['success' => true]);
    }

    private function validateRole(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100',
            'permissions' => 'nullable|array',
        ]);
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/i', '_', $value);
        return trim($value, '_');
    }
}
