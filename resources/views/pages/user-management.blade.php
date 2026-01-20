@extends('layouts.app')


@section('title', 'Manajemen User')

@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-8">
            <h2 class="font-bold text-xl text-emerald-900">
                Manajemen User
            </h2>
            <!-- Bisa tambahkan indikator di sini jika perlu -->
        </div>
    </div>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>
        <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition-all" id="btn-add-user">
            + Tambah User
        </button>
    </div>
    <div class="bg-white rounded-xl shadow p-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Nama</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Role</th>
                    <th class="px-4 py-2 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="px-4 py-2 text-gray-800">{{ $user->name }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $user->email }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $user->role ?? '-' }}</td>
                    <td class="px-4 py-2 text-center">
                        <button class="text-blue-500 hover:underline font-semibold mr-2">Edit</button>
                        <button class="text-red-500 hover:underline font-semibold">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
