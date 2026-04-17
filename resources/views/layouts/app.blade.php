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
    <div x-data="{ sidebarOpen: true }" class="min-h-screen bg-gray-50 flex flex-col w-full overflow-x-hidden">
        @include('layouts.navigation')

        <div class="flex flex-1 pt-20 w-full min-w-0">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 min-w-0 w-full overflow-x-hidden transition-all duration-300" :class="sidebarOpen ? 'md:ml-72' : 'md:ml-16'">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-20 z-40 overflow-x-hidden">
                        <div class="w-full max-w-7xl mx-auto py-4 px-3 sm:py-6 sm:px-6 lg:px-8 min-w-0">
                            {{ $header }}
                        </div>
                    </header>
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
</body>

</html>
