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
            <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
                <div class="w-full max-w-md">
                    <!-- Content Slot -->
                    {{ $slot }}
                </div>
            </div>

            <!-- Right Side - Illustration -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-emerald-700 via-emerald-800 to-emerald-900 items-center justify-center p-12 relative overflow-hidden">
                <!-- Decorative circles -->
                <div class="absolute top-10 right-10 w-72 h-72 bg-yellow-400 rounded-full opacity-10 blur-3xl"></div>
                <div class="absolute bottom-10 left-10 w-96 h-96 bg-emerald-500 rounded-full opacity-10 blur-3xl"></div>

                <div class="relative z-10 text-white max-w-lg">
                    <!-- Illustration Container -->
                    <div class="mb-8">
                        <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-8 border border-white/20">
                            <!-- Machine Icon -->
                            <div class="flex justify-center mb-6">
                                <div class="relative">
                                    <div class="w-48 h-48 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-3xl flex items-center justify-center shadow-2xl transform rotate-6">
                                        <svg class="w-32 h-32 text-emerald-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                        </svg>
                                    </div>
                                    <!-- Floating badge -->
                                    <div class="absolute -top-2 -right-2 bg-emerald-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                                        ✓ Active
                                    </div>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="bg-white/10 rounded-xl p-3">
                                    <div class="text-2xl font-bold text-yellow-400">24/7</div>
                                    <div class="text-xs text-white/70">Monitoring</div>
                                </div>
                                <div class="bg-white/10 rounded-xl p-3">
                                    <div class="text-2xl font-bold text-yellow-400">Real-time</div>
                                    <div class="text-xs text-white/70">Data</div>
                                </div>
                                <div class="bg-white/10 rounded-xl p-3">
                                    <div class="text-2xl font-bold text-yellow-400">Smart</div>
                                    <div class="text-xs text-white/70">Analysis</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Welcome Text -->
                    <div class="text-center">
                        <h2 class="text-4xl font-bold mb-4">
                            Selamat Datang!
                        </h2>
                        <p class="text-emerald-100 text-lg leading-relaxed">
                            Sistem monitoring dan analisis prediktif untuk mesin industri.
                            Pantau performa mesin secara real-time dan deteksi anomali lebih awal.
                        </p>
                    </div>

                    <!-- Features -->
                    <div class="mt-8 space-y-3">
                        <div class="flex items-center space-x-3 text-emerald-100">
                            <div class="w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span>Real-time Monitoring Data Sensor</span>
                        </div>
                        <div class="flex items-center space-x-3 text-emerald-100">
                            <div class="w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span>Analisis Prediktif Berbasis AI</span>
                        </div>
                        <div class="flex items-center space-x-3 text-emerald-100">
                            <div class="w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span>Notifikasi Deteksi Anomali</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
