<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->filled('search')) {
            $search = $request->input('search');
                        $query->where(function($q) use ($search) {
                                $q->where('name', 'like', "%$search%")
                                    ->orWhere('email', 'like', "%$search%")
                                    ->orWhere('role', 'like', "%$search%")
                                ;
                        });
        }
        $users = $query->get();
        $summary = [
            'total' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'koordinator' => User::where('role', 'koordinator')->count(),
            'super_admin' => User::where('role', 'super_admin')->count(),
        ];
        return view('pages.user-management', compact('users', 'summary'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'status' => 'required|boolean',
            'wa_notification_enabled' => 'required|boolean',
        ]);
        $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);
        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);
        return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'role' => 'required|string',
            'status' => 'required',
            'wa_notification_enabled' => 'required|boolean',
        ]);
        $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);
        // Cast status to boolean
        $validated['status'] = $validated['status'] == '1' ? 1 : 0;
        $validated['wa_notification_enabled'] = $validated['wa_notification_enabled'] == '1' ? 1 : 0;
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        }
        $user->update($validated);
        return response()->json(['success' => true, 'message' => 'User berhasil diupdate']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User berhasil dihapus']);
    }

    public function resetPassword(Request $request, $id)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::findOrFail($id);

        if (Hash::check($validated['new_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password baru tidak boleh sama dengan password sebelumnya.',
            ], 422);
        }

        $user->password = bcrypt($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password user berhasil diperbarui.',
        ]);
    }

    public function forceResetPassword(Request $request, $id)
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::findOrFail($id);

        if (Hash::check($validated['new_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password baru tidak boleh sama dengan password sebelumnya.',
            ], 422);
        }

        $user->password = bcrypt($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Reset password user berhasil.',
        ]);
    }
    // Show user detail for AJAX edit
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    private function normalizePhoneNumber(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? $phone;
    }
}
