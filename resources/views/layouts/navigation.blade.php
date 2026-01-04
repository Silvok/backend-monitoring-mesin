<nav x-data="{ open: false }" class="shadow-lg border-b-2 fixed top-0 left-0 right-0 z-50" style="background: linear-gradient(to right, #31694E, #275640); border-bottom-color: #1e4030;">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center space-x-4 sm:space-x-8 -ml-8 sm:-ml-16 md:-ml-20">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 hover:opacity-90 transition">
                        <div class="w-16 h-16 sm:w-24 sm:h-24 flex-shrink-0">
                            <img src="{{ asset('images/unnamed.png') }}" alt="PreMaint Logo" class="w-full h-full object-contain">
                        </div>
                        <span class="text-white font-black text-3xl sm:text-5xl bg-gradient-to-r from-white to-emerald-100 bg-clip-text text-transparent" style="font-weight: 900;">PreMaint</span>
                    </a>
                </div>
            </div>

            <!-- Right Side Menu -->
            <div class="hidden sm:flex items-center space-x-4 absolute right-0 pr-8">
                <!-- Notification Bell (Future) -->
                <button class="relative text-white hover:bg-white/10 p-2 rounded-lg transition duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-yellow-400 rounded-full"></span>
                </button>

                <!-- User Dropdown -->
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-full text-white font-semibold transition duration-200">
                        <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center font-bold text-xs shadow-lg">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden lg:flex items-center space-x-2">
                            <div class="text-left">
                                <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-emerald-200">{{ Auth::user()->email }}</p>
                            </div>
                            <svg :class="{ 'rotate-180': dropdownOpen }" class="w-4 h-4 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="dropdownOpen"
                         @click.outside="dropdownOpen = false"
                         x-transition
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-100">
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>

                        <!-- Menu Items -->
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 transition duration-150 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Profile</span>
                        </a>

                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 transition duration-150 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.26 2.632 1.732-.44.9.023 2.031.816 2.556 1.158.786 1.158 2.706 0 3.492-.793.525-1.256 1.656-.816 2.556.678 1.472-1.089 2.672-2.632 1.732a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.26-2.632-1.732.44-.9-.023-2.031-.816-2.556-1.158-.786-1.158-2.706 0-3.492.793-.525 1.256-1.656.816-2.556-.678-1.472 1.089-2.672 2.632-1.732a1.724 1.724 0 002.573-1.066z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Pengaturan</span>
                        </a>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 pt-2">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition duration-150 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Hamburger -->
            <div class="sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-lg text-white hover:bg-white/10 transition duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t-2" style="background-color: #275640; border-top-color: #1e4030;">
        <div class="px-4 pt-2 pb-3 space-y-1">
            <!-- Mobile menu items can be added here if needed -->
        </div>

        <!-- Mobile User Menu -->
        <div class="pt-4 pb-3 border-t" style="border-top-color: #31694E;">
            <div class="px-4 py-2">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-200">{{ Auth::user()->email }}</p>
            </div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 rounded text-white hover:bg-white/10 transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 rounded text-red-300 hover:bg-white/10 transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
