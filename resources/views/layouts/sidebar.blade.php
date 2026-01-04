<!-- Sidebar -->
<aside class="hidden md:flex flex-col w-72 bg-white border-r border-gray-100 h-screen fixed left-0 top-20">

    <!-- Sidebar Content -->
    <div class="flex-1 overflow-y-auto px-3 py-4">
        <nav class="space-y-1">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-700 font-medium transition duration-200
               @if(request()->routeIs('dashboard'))
                   bg-emerald-50 text-emerald-700
               @else
                   hover:bg-gray-50 text-gray-600
               @endif">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9M9 21h6" />
                </svg>
                <span class="text-sm">Dashboard</span>
            </a>

            <!-- Menu Section Title -->
            <div class="pt-4 pb-2">
                <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Monitoring</h3>
            </div>

            <!-- Real-time Monitoring -->
            <a href="{{ route('real-time-sensor') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg font-medium transition duration-200
               @if(request()->routeIs('real-time-sensor'))
                   bg-emerald-50 text-emerald-700
               @else
                   text-gray-600 hover:bg-gray-50 hover:text-gray-900
               @endif">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
                <span class="text-sm">Real-time Sensor</span>
            </a>

            <!-- Grafik -->
            <a href="{{ route('data-grafik') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-50 hover:text-gray-900 transition duration-200 {{ request()->routeIs('data-grafik') ? 'bg-blue-50 text-blue-600' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm">Data Grafik</span>
            </a>

            <!-- Analisis -->
            <a href="{{ route('analisis') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg font-medium transition duration-200
               @if(request()->routeIs('analisis'))
                   bg-purple-50 text-purple-700
               @else
                   text-gray-600 hover:bg-gray-50 hover:text-gray-900
               @endif">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span class="text-sm">Analisis</span>
            </a>

            <!-- Riwayat -->
            <a href="#"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-50 hover:text-gray-900 transition duration-200">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm">Riwayat Data</span>
            </a>

            <!-- Menu Section Title -->
            <div class="pt-4 pb-2">
                <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Alerts</h3>
            </div>

            <!-- Anomali -->
            <a href="#"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-50 hover:text-gray-900 transition duration-200 group">
                <div class="relative">
                    <svg class="w-5 h-5 flex-shrink-0 group-hover:text-red-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v2m0-10a4 4 0 110 8 4 4 0 010-8z" />
                    </svg>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                </div>
                <span class="text-sm">Anomali</span>
            </a>

            <!-- Peringatan -->
            <a href="#"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-50 hover:text-gray-900 transition duration-200 group">
                <div class="relative">
                    <svg class="w-5 h-5 flex-shrink-0 group-hover:text-yellow-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                </div>
                <span class="text-sm">Peringatan</span>
            </a>

            <!-- Menu Section Title -->
            <div class="pt-4 pb-2">
                <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Pengaturan</h3>
            </div>

            <!-- Konfigurasi -->
            <a href="#"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-50 hover:text-gray-900 transition duration-200">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.26 2.632 1.732-.44.9.023 2.031.816 2.556 1.158.786 1.158 2.706 0 3.492-.793.525-1.256 1.656-.816 2.556.678 1.472-1.089 2.672-2.632 1.732a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.26-2.632-1.732.44-.9-.023-2.031-.816-2.556-1.158-.786-1.158-2.706 0-3.492.793-.525 1.256-1.656.816-2.556-.678-1.472 1.089-2.672 2.632-1.732a1.724 1.724 0 002.573-1.066z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-sm">Pengaturan</span>
            </a>

        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="border-t border-gray-100 p-4">
        <div class="flex items-center space-x-3 bg-gradient-to-br from-emerald-50 to-teal-50 p-3 rounded-lg">
            <div class="w-9 h-9 bg-gradient-to-br from-emerald-700 to-emerald-800 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">Admin System</p>
            </div>
        </div>
    </div>
</aside>
