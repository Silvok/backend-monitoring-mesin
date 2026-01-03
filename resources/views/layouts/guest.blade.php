<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Machine Monitoring System</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
            <!-- Left Side - Form -->
            <div class="w-full lg:w-1/3 flex items-center justify-center p-8 bg-white">
                <div class="w-full max-w-sm px-4">
                    <!-- Content Slot -->
                    {{ $slot }}
                </div>
            </div>

            <!-- Right Side - Illustration -->
            <div class="hidden lg:flex lg:w-2/3 items-center justify-center p-12 relative overflow-hidden">
                <!-- Background Image Full -->
                <div class="absolute inset-0">
                    <img src="{{ asset('images/bgPdM.png') }}"
                         alt="Monitoring Equipment"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>

                <!-- Text Overlay -->
                <div class="relative z-10 max-w-3xl px-8 mx-auto">
                    <h2 class="text-white text-4xl font-bold mb-4 drop-shadow-2xl tracking-wide text-left">PreMaint</h2>
                    <p class="text-white text-xl font-semibold leading-relaxed drop-shadow-2xl tracking-wide text-left">
                        Sistem monitoring dan analisis prediktif untuk mesin industri. Pantau performa mesin secara real-time<br>
                        dan deteksi anomali lebih awal
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
