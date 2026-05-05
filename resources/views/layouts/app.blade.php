<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="idle-logout-minutes" content="{{ (int) config('session.idle_logout_minutes', 30) }}">
    <meta name="monitoring-login-url" content="{{ route('login') }}">
    <meta name="monitoring-logout-url" content="{{ route('logout') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-sans antialiased overflow-x-hidden w-full">
    <div x-data="{ sidebarOpen: true }" class="min-h-screen bg-gray-50 flex flex-col w-full">
        @include('layouts.navigation')

        <div class="flex flex-1 pt-16 sm:pt-20 w-full min-w-0">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 min-w-0 w-full" :class="sidebarOpen ? 'md:ml-72' : 'md:ml-16'">
                <!-- Page Heading -->
                @php($isRealtimeHeader = request()->routeIs('real-time-sensor'))
                @isset($header)
                    <header
                        class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 z-40 {{ $isRealtimeHeader ? 'fixed top-16 sm:top-20 left-0 right-0 md:left-72' : 'sticky top-16 sm:top-20' }}"
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
</body>

</html>
