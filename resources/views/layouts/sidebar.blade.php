<!-- Sidebar -->
<aside class="hidden md:flex flex-col bg-white border-r border-gray-100 h-screen fixed left-0 top-20 transition-all duration-300"
    :class="sidebarOpen ? 'w-72' : 'w-16'">

    <!-- Toggle Button Header -->
    <div class="flex items-center px-3 py-3 border-b border-gray-100"
        :class="sidebarOpen ? 'justify-end' : 'justify-center px-2'">
        <button @click="sidebarOpen = !sidebarOpen"
            class="w-10 h-10 bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-lg flex items-center justify-center transition-all duration-200 group"
            :title="sidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar'">
            <svg class="w-5 h-5 text-gray-500 group-hover:text-emerald-600 transition-transform duration-300"
                :class="sidebarOpen ? '' : 'rotate-180'"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <!-- Sidebar Content -->
    <div class="flex-1 overflow-y-auto px-3 py-4" :class="sidebarOpen ? '' : 'px-2'">
        <nav class="space-y-1">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 rounded-lg text-gray-700 font-medium transition duration-200
               @if(request()->routeIs('dashboard'))
                   text-gray-900
               @else
                   hover:bg-gray-50 text-gray-600
               @endif"
               :class="sidebarOpen ? 'space-x-3' : 'justify-center px-2'"
               @if(request()->routeIs('dashboard'))
                style="background: linear-gradient(to right, rgba(49, 105, 78, 0.35), rgba(39, 86, 64, 0.35)); color: #163527;"
            @endif
            :title="sidebarOpen ? '' : '{{ __('messages.app.dashboard') }}'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9M9 21h6" />
                </svg>
                <span class="text-sm" x-show="sidebarOpen" x-transition>{{ __('messages.app.dashboard') }}</span>
            </a>

            <!-- Menu Section Title -->
            <div class="pt-4 pb-2" x-show="sidebarOpen" x-transition>
                <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('messages.app.monitoring') }}</h3>
            </div>
            <div class="pt-4 pb-2 flex justify-center" x-show="!sidebarOpen">
                <div class="w-8 h-px bg-gray-200"></div>
            </div>

            <!-- Real-time Monitoring -->
            <a href="{{ route('real-time-sensor') }}" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition duration-200
               @if(request()->routeIs('real-time-sensor'))
                   text-gray-900
               @else
                   text-gray-600 hover:bg-gray-50 hover:text-gray-900
               @endif"
               :class="sidebarOpen ? 'space-x-3' : 'justify-center px-2'"
               @if(request()->routeIs('real-time-sensor'))
                style="background: linear-gradient(to right, rgba(49, 105, 78, 0.35), rgba(39, 86, 64, 0.35)); color: #163527;"
            @endif
            :title="sidebarOpen ? '' : '{{ __('messages.app.realtime') }}'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
                <span class="text-sm" x-show="sidebarOpen" x-transition>{{ __('messages.app.realtime') }}</span>
            </a>

            <!-- Monitoring Mesin -->
            <a href="{{ route('monitoring-mesin') }}" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition duration-200
               @if(request()->routeIs('monitoring-mesin'))
                   text-gray-900
               @else
                   text-gray-600 hover:bg-gray-50 hover:text-gray-900
               @endif"
               :class="sidebarOpen ? 'space-x-3' : 'justify-center px-2'"
               @if(request()->routeIs('monitoring-mesin'))
                style="background: linear-gradient(to right, rgba(49, 105, 78, 0.35), rgba(39, 86, 64, 0.35)); color: #163527;"
            @endif
            :title="sidebarOpen ? '' : '{{ __('messages.app.monitoring_machine') }}'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm" x-show="sidebarOpen" x-transition>{{ __('messages.app.monitoring_machine') }}</span>
            </a>

            <!-- Parameter Monitoring -->
            <a href="{{ route('parameter-monitoring') }}" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition duration-200
               @if(request()->routeIs('parameter-monitoring'))
                   text-gray-900
               @else
                   text-gray-600 hover:bg-gray-50 hover:text-gray-900
               @endif"
               :class="sidebarOpen ? 'space-x-3' : 'justify-center px-2'"
               @if(request()->routeIs('parameter-monitoring'))
                style="background: linear-gradient(to right, rgba(49, 105, 78, 0.35), rgba(39, 86, 64, 0.35)); color: #163527;"
            @endif
            :title="sidebarOpen ? '' : '{{ __('messages.app.parameter_monitoring') }}'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 3v18m0 0l-3-3m3 3l3-3M4 7h7m-7 5h7m-7 5h7" />
                </svg>
                <span class="text-sm" x-show="sidebarOpen" x-transition>{{ __('messages.app.parameter_monitoring') }}</span>
            </a>

            <!-- Laporan Bulanan -->
            <a href="{{ route('monthly-report') }}" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition duration-200
               @if(request()->routeIs('monthly-report'))
                   text-gray-900
               @else
                   text-gray-600 hover:bg-gray-50 hover:text-gray-900
               @endif"
               :class="sidebarOpen ? 'space-x-3' : 'justify-center px-2'"
               @if(request()->routeIs('monthly-report'))
                style="background: linear-gradient(to right, rgba(49, 105, 78, 0.35), rgba(39, 86, 64, 0.35)); color: #163527;"
            @endif
            :title="sidebarOpen ? '' : 'Laporan Bulanan'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span class="text-sm" x-show="sidebarOpen" x-transition>Laporan Bulanan</span>
            </a>


            <!-- Menu Section Title -->
            <div class="pt-4 pb-2" x-show="sidebarOpen" x-transition>
                <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('messages.app.alerts') }}</h3>
            </div>
            <div class="pt-4 pb-2 flex justify-center" x-show="!sidebarOpen">
                <div class="w-8 h-px bg-gray-200"></div>
            </div>

            <!-- Manajemen Alert -->
            <a href="{{ route('alert-management') }}" class="flex items-center px-4 py-2.5 rounded-lg font-medium transition duration-200
               @if(request()->routeIs('alert-management'))
                   text-gray-900
               @else
                   text-gray-600 hover:bg-gray-50 hover:text-gray-900
               @endif"
               :class="sidebarOpen ? 'space-x-3' : 'justify-center px-2'"
               @if(request()->routeIs('alert-management'))
                style="background: linear-gradient(to right, rgba(49, 105, 78, 0.35), rgba(39, 86, 64, 0.35)); color: #163527;"
            @endif
            :title="sidebarOpen ? '' : '{{ __('messages.app.alert_management') }}'">
                <div class="relative">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                </div>
                <span class="text-sm" x-show="sidebarOpen" x-transition>{{ __('messages.app.alert_management') }}</span>
            </a>

            @if(in_array(Auth::user()->role, ['admin', 'super_admin'], true))
                <!-- Menu Section Title -->
                <div class="pt-4 pb-2" x-show="sidebarOpen" x-transition>
                    <h3 class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('messages.app.settings') }}</h3>
                </div>
                <div class="pt-4 pb-2 flex justify-center" x-show="!sidebarOpen">
                    <div class="w-8 h-px bg-gray-200"></div>
                </div>

                <!-- Konfigurasi -->
                <a href="{{ route('settings') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-gray-50 hover:text-gray-900 transition duration-200"
                    :class="sidebarOpen ? 'space-x-3' : 'justify-center px-2'"
                    @if(request()->routeIs('settings'))
                        style="background: linear-gradient(to right, rgba(49, 105, 78, 0.35), rgba(39, 86, 64, 0.35)); color: #163527;"
                    @endif
                :title="sidebarOpen ? '' : '{{ __('messages.app.settings') }}'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.26 2.632 1.732-.44.9.023 2.031.816 2.556 1.158.786 1.158 2.706 0 3.492-.793.525-1.256 1.656-.816 2.556.678 1.472-1.089 2.672-2.632 1.732a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.26-2.632-1.732.44-.9-.023-2.031-.816-2.556-1.158-.786-1.158-2.706 0-3.492.793-.525 1.256-1.656.816-2.556-.678-1.472 1.089-2.672 2.632-1.732a1.724 1.724 0 002.573-1.066z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-sm" x-show="sidebarOpen" x-transition>{{ __('messages.app.settings') }}</span>
                </a>

                <!-- User Management (Manajemen User) -->
                <a href="{{ route('user-management') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-emerald-50 hover:text-emerald-900 transition duration-200"
                    :class="sidebarOpen ? 'space-x-3' : 'justify-center px-2'"
                    @if(request()->routeIs('user-management'))
                        style="background: linear-gradient(to right, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15)); color: #065f46;"
                    @endif
                    :title="sidebarOpen ? '' : '{{ __('messages.app.user_management') }}'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h6M3 20h5v-2a4 4 0 013-3.87M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75" />
                    </svg>
                    <span class="text-sm" x-show="sidebarOpen" x-transition>{{ __('messages.app.user_management') }}</span>
                </a>

                @if(Auth::user()->role === 'super_admin')
                    <!-- Role Management -->
                    <a href="{{ route('role-management') }}"
                        class="flex items-center px-4 py-2.5 rounded-lg text-gray-600 font-medium hover:bg-emerald-50 hover:text-emerald-900 transition duration-200"
                        :class="sidebarOpen ? 'space-x-3' : 'justify-center px-2'"
                        @if(request()->routeIs('role-management'))
                            style="background: linear-gradient(to right, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15)); color: #065f46;"
                        @endif
                        :title="sidebarOpen ? '' : 'Role Management'">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-6-8h6M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span class="text-sm" x-show="sidebarOpen" x-transition>Role Management</span>
                    </a>
                @endif
            @endif

        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="border-t border-gray-100 p-4" :class="sidebarOpen ? '' : 'p-2'">
        <div class="flex items-center bg-gradient-to-br from-emerald-50 to-teal-50 p-3 rounded-lg"
            :class="sidebarOpen ? 'space-x-3' : 'justify-center p-2'">
            <div
                class="w-9 h-9 bg-gradient-to-br from-emerald-700 to-emerald-800 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0"
                :class="sidebarOpen ? '' : 'w-8 h-8'">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0" x-show="sidebarOpen" x-transition>
                <p class="text-xs font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">Admin System</p>
            </div>
        </div>
    </div>
</aside>
