<nav x-data="{ open: false }" class="shadow-lg border-b-2 fixed top-0 left-0 right-0 z-50"
    style="background: linear-gradient(to right, #31694E, #275640); border-bottom-color: #1e4030;">
    <!-- Primary Navigation Menu -->
    <div class="w-full px-4">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center ml-12">
                <!-- Logo & Brand -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 hover:opacity-90 transition">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0">
                            <img src="{{ asset('images/unnamed.png') }}" alt="PreMaint Logo"
                                class="w-full h-full object-contain">
                        </div>
                        <div class="leading-tight">
                            <span
                                class="text-white font-black text-2xl sm:text-3xl block"
                                style="font-weight: 900;">PreMaint</span>
                            <span class="text-emerald-100 text-base sm:text-lg font-semibold block">Prediktif Maintenance</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Right Side Menu -->
            <div class="hidden sm:flex items-center space-x-4 absolute right-0 pr-8">
                <!-- Notification Bell -->
                <button id="notifBtn" type="button" class="relative text-white hover:bg-white/10 p-2 rounded-lg transition duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span id="notifBadge" class="absolute -top-1 -right-1 min-w-[18px] px-1.5 py-0.5 text-[10px] font-bold bg-red-500 text-white rounded-full hidden"></span>
                </button>
                <div id="notifMenu" class="fixed top-24 w-72 max-w-[calc(100vw-2rem)] max-h-[calc(100vh-6rem)] -translate-x-1/2 bg-white rounded-2xl shadow-2xl border border-gray-100 hidden z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ __('messages.notifications.title') }}</p>
                                    <p class="text-[11px] text-gray-500">{{ __('messages.notifications.subtitle') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="notifUnreadPill" class="text-[10px] font-bold px-2 py-1 rounded-full bg-gray-900 text-white hidden"></span>
                                <button id="notifReadAll" class="text-[10px] font-semibold px-2.5 py-1 rounded-full border border-gray-200 text-gray-700 hover:bg-gray-50">
                                    {{ __('messages.notifications.mark_all') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="notifList" class="max-h-64 overflow-y-auto bg-gray-50/70">
                        <div class="px-4 py-6 text-center text-sm text-gray-500">{{ __('messages.notifications.loading') }}</div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen"
                        class="flex items-center space-x-2 hover:bg-white/10 px-3 py-2 rounded-full text-white font-semibold transition duration-200">
                        <div
                            class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center font-bold text-xs shadow-lg">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden lg:flex items-center space-x-2">
                            <div class="text-left">
                                <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-emerald-200">{{ Auth::user()->email }}</p>
                            </div>
                            <svg :class="{ 'rotate-180': dropdownOpen }" class="w-4 h-4 transition duration-200"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition
                        class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 border border-gray-100">
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>

                        <!-- Menu Items -->
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 transition duration-150 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ __('messages.app.profile') }}</span>
                        </a>

                        <a href="#"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 transition duration-150 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.26 2.632 1.732-.44.9.023 2.031.816 2.556 1.158.786 1.158 2.706 0 3.492-.793.525-1.256 1.656-.816 2.556.678 1.472-1.089 2.672-2.632 1.732a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.26-2.632-1.732.44-.9-.023-2.031-.816-2.556-1.158-.786-1.158-2.706 0-3.492.793-.525 1.256-1.656.816-2.556-.678-1.472 1.089-2.672 2.632-1.732a1.724 1.724 0 002.573-1.066z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ __('messages.app.settings') }}</span>
                        </a>

                        <!-- Logout -->
                        <div class="border-t border-gray-100 pt-2">
                            <a href="{{ route('logout') }}"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition duration-150 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>{{ __('messages.app.logout') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Hamburger -->
            <div class="sm:hidden">
                <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-lg text-white hover:bg-white/10 transition duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t-2"
        style="background-color: #275640; border-top-color: #1e4030;">
        <div class="px-4 pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                class="text-white hover:bg-white/10 hover:text-white">
                {{ __('messages.app.dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('real-time-sensor')" :active="request()->routeIs('real-time-sensor')"
                class="text-white hover:bg-white/10 hover:text-white">
                {{ __('messages.app.realtime') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('monitoring-mesin')" :active="request()->routeIs('monitoring-mesin')"
                class="text-white hover:bg-white/10 hover:text-white">
                {{ __('messages.app.monitoring_machine') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('analisis')" :active="request()->routeIs('analisis')"
                class="text-white hover:bg-white/10 hover:text-white">
                {{ __('messages.app.analysis') }}
            </x-responsive-nav-link>

            <div class="pt-2 pb-1 border-t border-emerald-800/50 mt-2">
                <div class="px-4 text-xs font-semibold text-emerald-200/50 uppercase tracking-wider mb-2">{{ __('messages.app.alerts_settings') }}</div>
                <a href="#"
                    class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-emerald-100 hover:text-white hover:bg-emerald-800/50 hover:border-emerald-300 transition duration-150 ease-in-out">
                    Anomali
                </a>
                <a href="#"
                    class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-emerald-100 hover:text-white hover:bg-emerald-800/50 hover:border-emerald-300 transition duration-150 ease-in-out">
                    Pengaturan
                </a>
            </div>
        </div>

        <!-- Mobile User Menu -->
        <div class="pt-4 pb-3 border-t" style="border-top-color: #31694E;">
            <div class="px-4 py-2">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-200">{{ Auth::user()->email }}</p>
            </div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}"
                    class="block px-4 py-2 rounded text-white hover:bg-white/10 transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>{{ __('messages.app.profile') }}</span>
                </a>
                <a href="{{ route('logout') }}"
                    class="block w-full text-left px-4 py-2 rounded text-red-300 hover:bg-white/10 transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>{{ __('messages.app.logout') }}</span>
                </a>
            </div>
        </div>
    </div>
</nav>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('notifBtn');
        const menu = document.getElementById('notifMenu');
        const list = document.getElementById('notifList');
        const badge = document.getElementById('notifBadge');
        const markAll = document.getElementById('notifReadAll');
        const unreadPill = document.getElementById('notifUnreadPill');

        if (!btn || !menu || !list || !badge || !markAll) {
            return;
        }

        function positionMenu() {
            const rect = btn.getBoundingClientRect();
            menu.style.top = `${rect.bottom + 24}px`;
            menu.style.left = `${rect.left + (rect.width / 2)}px`;
            menu.style.right = 'auto';
        }

        const notifText = {
            unread: @json(__('messages.notifications.unread')),
            empty: @json(__('messages.notifications.empty')),
            error: @json(__('messages.notifications.error')),
        };

        function renderNotifications(data) {
            const items = data.items || [];
            const unread = data.unread_count || 0;
            badge.textContent = unread > 99 ? '99+' : String(unread);
            badge.classList.toggle('hidden', unread === 0);
            if (unreadPill) {
                unreadPill.textContent = `${unread} ${notifText.unread}`;
                unreadPill.classList.toggle('hidden', unread === 0);
            }

            if (!items.length) {
                list.innerHTML = `<div class="px-4 py-6 text-center text-sm text-gray-500">${notifText.empty}</div>`;
                return;
            }

            list.innerHTML = items.map(item => {
                const severity = (item.severity || 'INFO').toUpperCase();
                const iconClass = severity === 'CRITICAL'
                    ? 'text-red-600 border-red-200 bg-red-50'
                    : severity === 'WARNING'
                        ? 'text-yellow-600 border-yellow-200 bg-yellow-50'
                        : 'text-emerald-600 border-emerald-200 bg-emerald-50';
                const badgeClass = severity === 'CRITICAL'
                    ? 'bg-red-100 text-red-700'
                    : severity === 'WARNING'
                        ? 'bg-yellow-100 text-yellow-700'
                        : 'bg-emerald-100 text-emerald-700';
                const rowClass = item.is_read ? 'bg-white' : 'bg-emerald-50/50';
                const statusDotClass = item.is_read ? 'bg-gray-300' : 'bg-emerald-500';
                return `
                    <button data-id="${item.id}" class="notif-item w-full text-left px-4 py-3 border-b border-gray-100 hover:bg-emerald-50 ${rowClass}">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full border ${iconClass} flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M12 18a6 6 0 100-12 6 6 0 000 12z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-gray-900">${item.title}</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full ${badgeClass}">${severity}</span>
                                        <span class="w-2 h-2 rounded-full ${statusDotClass}"></span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">${item.message || ''}</p>
                                <p class="text-[11px] text-gray-400 mt-2">${item.time_ago}</p>
                            </div>
                        </div>
                    </button>
                `;
            }).join('');
        }

        function loadNotifications() {
            fetch('/notifications')
                .then(r => r.json())
                .then(renderNotifications)
                .catch(() => {
                    list.innerHTML = `<div class="px-4 py-6 text-center text-sm text-gray-500">${notifText.error}</div>`;
                });
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('hidden');
            if (!menu.classList.contains('hidden')) {
                positionMenu();
                loadNotifications();
            }
        });

        document.addEventListener('click', function (e) {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        list.addEventListener('click', function (e) {
            const item = e.target.closest('.notif-item');
            if (!item) return;
            const id = item.getAttribute('data-id');
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).finally(() => {
                    item.remove();
                    if (!list.querySelector('.notif-item')) {
                        list.innerHTML = `<div class="px-4 py-6 text-center text-sm text-gray-500">${notifText.empty}</div>`;
                    }
                    window.location.href = '/alert-management';
                });
            });
        });

        markAll.addEventListener('click', function () {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => loadNotifications());
        });

        window.addEventListener('resize', function () {
            if (!menu.classList.contains('hidden')) {
                positionMenu();
            }
        });

        loadNotifications();
        setInterval(loadNotifications, 30000);
    });
</script>
@endpush
