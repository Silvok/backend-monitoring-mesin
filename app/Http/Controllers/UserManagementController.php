<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('pages.user-management', compact('users'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'status' => 'required|boolean',
        ]);
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
            'role' => 'required|string',
            'status' => 'required|boolean',
        ]);
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

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password = bcrypt('12345678');
        $user->save();
        return response()->json(['success' => true, 'message' => 'Password berhasil direset ke default']);
    }
}
