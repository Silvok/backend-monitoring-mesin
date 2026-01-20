<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-emerald-900">Manajemen User</h2>
    </x-slot>
    <div class="max-w-2xl mx-auto mt-8">
        <div class="space-y-2">
            @foreach($users as $user)
                <div class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-full text-white font-semibold transition duration-200 {{ $user->email === 'admin@example.com' ? 'bg-emerald-600' : 'bg-emerald-400' }}">
                    <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center font-bold text-xs shadow-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-bold">{{ $user->name }}</div>
                        <div class="text-xs">{{ $user->email }}</div>
                    </div>
                    @if($user->email === 'admin@example.com')
                        <span class="px-2 py-1 text-xs rounded bg-white text-emerald-700 font-bold">Admin</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-white text-gray-700">User</span>
                        <button class="ml-2 px-2 py-1 text-xs bg-red-500 text-white rounded">Hapus</button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
<!-- Halaman ini telah dihapus. -->
