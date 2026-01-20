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
    // Tambah, edit, hapus user bisa ditambah di sini
}
