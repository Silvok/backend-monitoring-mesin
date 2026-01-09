<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl" style="color: #185519;">
                    Data Grafik
                </h2>
                <!-- Live Status Indicator -->
                <div class="flex items-center space-x-2 px-3 py-1.5 rounded-full border" style="background-color: #f0faf3; border-color: #b3e5c0;">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: #2bc970;"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3" style="background-color: #118B50;"></span>
                    </div>
                    <span class="text-xs font-semibold" style="color: #185519;">Live</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold" id="currentTime">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d M Y, H:i') }}</span>
                </div>
                <button onclick="refreshDashboard()" class="px-4 py-1.5 text-white text-sm font-medium rounded-lg transition flex items-center space-x-2 shadow-sm" style="background-color: #118B50;" onmouseover="this.style.backgroundColor='#185519'" onmouseout="this.style.backgroundColor='#118B50'">
                    <svg id="refreshIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Panel -->
            <div id="alertPanel" class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden border-l-4 border-red-500" style="display: none;">
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="text-xl font-bold text-white">
                                Alert Panel - Anomali Terdeteksi
                            </h3>
                            <span id="alertCount" class="bg-white text-red-600 px-3 py-1 rounded-full text-sm font-bold">0</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="toggleAlertSound()" id="soundToggle" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition flex items-center space-x-2">
                                <svg id="soundIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                                </svg>
                                <span id="soundText">On</span>
                            </button>
                            <button onclick="dismissAllAlerts()" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition">
                                Dismiss All
                            </button>
                            <button onclick="toggleAlertPanel()" class="bg-white/20 hover:bg-white/30 text-white p-1.5 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main Content -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #185519;">Data Grafik</h3>
                <!-- Content for Data Grafik will go here -->
            </div>
        </div>
    </div>
</x-app-layout>
