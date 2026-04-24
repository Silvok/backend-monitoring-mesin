<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <!-- Alpine.js for interactivity -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="font-sans antialiased overflow-x-hidden w-full">
    <div x-data="{ sidebarOpen: true }" class="min-h-screen bg-gray-50 flex flex-col w-full">
        @include('layouts.navigation')

        <div class="flex flex-1 pt-16 sm:pt-20 w-full min-w-0">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 min-w-0 w-full transition-all duration-300" :class="sidebarOpen ? 'md:ml-72' : 'md:ml-16'">
                <!-- Page Heading -->
                @php($isRealtimeHeader = request()->routeIs('real-time-sensor'))
                @isset($header)
                    <header
                        class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 z-40 {{ $isRealtimeHeader ? 'fixed top-16 sm:top-20 left-0 right-0 md:left-72 transition-all duration-300' : 'sticky top-16 sm:top-20' }}"
                        @if($isRealtimeHeader)
                            data-fixed-realtime="1"
                            :class="sidebarOpen ? 'md:left-72' : 'md:left-16'"
                        @endif
                    >
                        <div class="w-full max-w-7xl mx-auto py-4 px-3 sm:py-6 sm:px-6 lg:px-8 min-w-0">
                            {{ $header }}
                        </div>
                    </header>
                    @if($isRealtimeHeader)
                        <div id="fixedHeaderSpacer" class="w-full"></div>
                    @endif
                @endisset

                <!-- Page Content -->
                <main class="w-full min-w-0 overflow-x-hidden">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    <!-- Scripts Stack -->
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fixedHeader = document.querySelector('header[data-fixed-realtime="1"]');
            const spacer = document.getElementById('fixedHeaderSpacer');
            if (!fixedHeader || !spacer) return;

            const syncFixedHeaderSpacer = () => {
                spacer.style.height = `${fixedHeader.offsetHeight}px`;
            };

            syncFixedHeaderSpacer();
            window.addEventListener('resize', syncFixedHeaderSpacer);
            setTimeout(syncFixedHeaderSpacer, 120);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const path = window.location.pathname.replace(/\/+$/, '');
            if (path !== '/monitoring-mesin') return;

            const headerSlot = document.querySelector('header > div > div');
            if (!headerSlot) return;

            const titleEl = headerSlot.querySelector('h2');
            const timeEl = headerSlot.querySelector('#currentTime');
            if (!titleEl || !timeEl) return;

            // Remove legacy live/connected chip if still rendered from stale template.
            const liveText = ['Terhubung', 'Live', 'Connected'];
            headerSlot.querySelectorAll('span').forEach((span) => {
                if (!liveText.includes((span.textContent || '').trim())) return;
                const chip = span.closest('div');
                if (chip) chip.remove();
            });

            headerSlot.className = 'w-full min-w-0 flex items-center justify-between gap-2';

            const leftWrap = titleEl.closest('div');
            if (leftWrap) leftWrap.className = 'min-w-0 flex-1';
            titleEl.className = 'font-bold text-base sm:text-xl text-emerald-900 truncate';

            const timeBox = timeEl.parentElement;
            const rightWrap = timeBox && timeBox.parentElement ? timeBox.parentElement : null;
            if (rightWrap) rightWrap.className = 'flex-shrink-0';
            if (timeBox) {
                timeBox.className = 'inline-flex items-center text-[10px] sm:text-sm text-gray-600 bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-200';
            }
            timeEl.className = 'font-semibold whitespace-nowrap tabular-nums';

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            function updateMonitoringHeaderClock() {
                const now = new Date();
                timeEl.textContent = `${String(now.getDate()).padStart(2, '0')} ${months[now.getMonth()]} ${now.getFullYear()}, ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
            }

            updateMonitoringHeaderClock();
            setInterval(updateMonitoringHeaderClock, 1000);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const idleMinutes = Number(@json((int) config('session.idle_logout_minutes', 30)));
            if (!Number.isFinite(idleMinutes) || idleMinutes <= 0) return;

            const idleMs = idleMinutes * 60 * 1000;
            const syncKey = 'monitoring:last-activity-at';
            const forceLogoutKey = 'monitoring:force-logout-at';
            const loginUrl = @json(route('login'));
            const logoutUrl = @json(route('logout'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            let lastActivityAt = Date.now();
            let hiddenSince = document.hidden ? Date.now() : null;
            let isLoggingOut = false;
            let lastSyncedAt = 0;

            function syncActivity(ts) {
                if (ts - lastSyncedAt < 5000) return;
                lastSyncedAt = ts;

                try {
                    localStorage.setItem(syncKey, String(ts));
                } catch (_) {
                    // no-op (private mode / storage disabled)
                }
            }

            function markActivity() {
                if (isLoggingOut) return;
                const now = Date.now();
                lastActivityAt = now;
                syncActivity(now);
            }

            function pullSharedActivity() {
                try {
                    const value = localStorage.getItem(syncKey);
                    const parsed = Number(value);
                    if (Number.isFinite(parsed) && parsed > lastActivityAt) {
                        lastActivityAt = parsed;
                    }
                } catch (_) {
                    // no-op
                }
            }

            async function logoutDueToIdle() {
                if (isLoggingOut) return;
                isLoggingOut = true;

                try {
                    localStorage.setItem(forceLogoutKey, String(Date.now()));
                } catch (_) {
                    // no-op
                }

                try {
                    await fetch(logoutUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                } catch (_) {
                    // if request fails (expired session/network), continue redirect
                }

                window.location.href = loginUrl;
            }

            function checkIdleTimeout() {
                if (isLoggingOut) return;
                pullSharedActivity();

                const now = Date.now();
                if (hiddenSince !== null && (now - hiddenSince) >= idleMs) {
                    logoutDueToIdle();
                    return;
                }

                if ((now - lastActivityAt) >= idleMs) {
                    logoutDueToIdle();
                }
            }

            const activityEvents = [
                'mousemove',
                'mousedown',
                'keydown',
                'scroll',
                'touchstart',
                'click',
            ];

            activityEvents.forEach((eventName) => {
                window.addEventListener(eventName, markActivity, { passive: true });
            });

            document.addEventListener('visibilitychange', function () {
                if (document.hidden) {
                    hiddenSince = Date.now();
                    return;
                }

                const now = Date.now();
                if (hiddenSince !== null && (now - hiddenSince) >= idleMs) {
                    logoutDueToIdle();
                    return;
                }

                hiddenSince = null;
                markActivity();
            });

            window.addEventListener('storage', function (event) {
                if (event.key === syncKey && event.newValue) {
                    const parsed = Number(event.newValue);
                    if (Number.isFinite(parsed) && parsed > lastActivityAt) {
                        lastActivityAt = parsed;
                    }
                    return;
                }

                if (event.key === forceLogoutKey && event.newValue) {
                    window.location.href = loginUrl;
                }
            });

            markActivity();
            pullSharedActivity();

            const idleCheckIntervalMs = Math.min(15000, Math.max(3000, Math.floor(idleMs / 6)));
            const idleTimer = setInterval(checkIdleTimeout, idleCheckIntervalMs);

            window.addEventListener('beforeunload', function () {
                clearInterval(idleTimer);
            });
        });
    </script>
</body>

</html>
