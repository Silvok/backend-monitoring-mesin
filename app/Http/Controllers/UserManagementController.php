<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
            'teknisi' => User::whereIn('role', ['teknisi', 'operator'])->count(),
        ];
        return view('pages.user-management', compact('users', 'summary'));
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
            'status' => 'required',
        ]);
        // Cast status to boolean
        $validated['status'] = $validated['status'] == '1' ? 1 : 0;
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
    // Show user detail for AJAX edit
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
}
