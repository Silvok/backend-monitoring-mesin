<!-- Alert Panel -->
<div id="alertPanel" class="bg-white rounded-xl shadow-lg mb-8 overflow-hidden border-l-4 border-red-500" style="display: none;">
    <div class="bg-gradient-to-r from-red-600 to-red-700 px-4 sm:px-6 py-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h3 class="text-lg sm:text-xl font-bold text-white">
                    {{ __('messages.dashboard.alert_panel') }}
                </h3>
                <span id="alertCount" class="bg-white text-red-600 px-3 py-1 rounded-full text-sm font-bold">0</span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="toggleAlertSound()" id="soundToggle" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition flex items-center space-x-2">
                    <svg id="soundIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                    </svg>
                    <span id="soundText">On</span>
                </button>
                <button onclick="dismissAllAlerts()" class="bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition">
                    {{ __('messages.dashboard.dismiss_all') }}
                </button>
                <button onclick="toggleAlertPanel()" class="bg-white/20 hover:bg-white/30 text-white p-1.5 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Filters -->
    <div class="bg-gray-50 px-4 sm:px-6 py-3 border-b border-gray-200">
        <div class="flex flex-wrap items-center gap-2 sm:gap-4">
            <span class="text-sm font-semibold text-gray-700">{{ __('messages.dashboard.filter') }}:</span>
            <button onclick="filterAlerts('all')" class="alert-filter-btn active px-3 py-1 rounded-lg text-sm font-medium transition">
                {{ __('messages.dashboard.filter_all') }}
            </button>
            <button onclick="filterAlerts('critical')" class="alert-filter-btn px-3 py-1 rounded-lg text-sm font-medium transition">
                {{ __('messages.dashboard.filter_critical') }}
            </button>
            <button onclick="filterAlerts('high')" class="alert-filter-btn px-3 py-1 rounded-lg text-sm font-medium transition">
                {{ __('messages.dashboard.filter_high') }}
            </button>
            <button onclick="filterAlerts('unacknowledged')" class="alert-filter-btn px-3 py-1 rounded-lg text-sm font-medium transition">
                {{ __('messages.dashboard.filter_unack') }}
            </button>
        </div>
    </div>

    <!-- Alert List -->
    <div id="alertList" class="max-h-96 overflow-y-auto">
        <!-- Alerts will be dynamically loaded here -->
        <div class="px-6 py-8 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>{{ __('messages.dashboard.loading_alerts') }}</p>
        </div>
    </div>
</div>
