<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleManagementController extends Controller
{
    public function index()
    {
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

        return view('pages.role-management', compact('roles', 'permissionGroups', 'permissionLabels'));
    }

    public function store(Request $request)
    {
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
        if ($role->is_protected) {
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

    public function destroy(Role $role)
    {
        if ($role->is_protected) {
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
