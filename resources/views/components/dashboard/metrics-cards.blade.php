<!-- Metrics Cards -->
<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-8">
    <!-- Total Mesin -->
    <div class="rounded-xl shadow-lg p-4 sm:p-6 text-white" style="background: linear-gradient(to bottom right, #185519, #118B50);">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="rounded-lg p-2.5 sm:p-3" style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
            </div>
            <span class="text-2xl sm:text-3xl font-bold" id="totalMachines">{{ $totalMachines }}</span>
        </div>
        <h3 class="text-base sm:text-lg font-semibold">{{ __('messages.dashboard.metrics_total_machines') }}</h3>
        <p class="text-xs sm:text-sm mt-1" style="color: rgba(255,255,255,0.8);">{{ __('messages.dashboard.metrics_machines_monitored') }}</p>
    </div>

    <!-- Total Samples -->
    <div class="rounded-xl shadow-lg p-4 sm:p-6 text-white" style="background-color: #187498;">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="rounded-lg p-2.5 sm:p-3" style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <span class="text-2xl sm:text-3xl font-bold">{{ number_format($totalSamples) }}</span>
        </div>
        <h3 class="text-base sm:text-lg font-semibold">{{ __('messages.dashboard.metrics_sensor_data') }}</h3>
        <p class="text-xs sm:text-sm mt-1" style="color: rgba(255,255,255,0.8);">{{ __('messages.dashboard.metrics_total_samples') }}</p>
    </div>

    <!-- Total Analysis -->
    <div class="rounded-xl shadow-lg p-4 sm:p-6 text-white" style="background: linear-gradient(to bottom right, #FCCD2A, #F4B942);">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="rounded-lg p-2.5 sm:p-3" style="background-color: rgba(255,255,255,0.4);">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
            <span class="text-2xl sm:text-3xl font-bold">{{ number_format($totalAnalysis) }}</span>
        </div>
        <h3 class="text-base sm:text-lg font-semibold text-white">{{ __('messages.dashboard.metrics_analysis') }}</h3>
        <p class="text-xs sm:text-sm mt-1" style="color: rgba(255,255,255,0.85);">{{ __('messages.dashboard.metrics_total_analysis') }}</p>
    </div>

    <!-- Status Anomali -->
    <div class="rounded-xl shadow-lg p-4 sm:p-6 text-white" style="background: linear-gradient(to bottom right, #B45253, #8B3E3E);">
        <div class="flex items-center justify-between mb-3 sm:mb-4">
            <div class="rounded-lg p-2.5 sm:p-3" style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="text-2xl sm:text-3xl font-bold">{{ $anomalyCount }}</span>
        </div>
        <h3 class="text-base sm:text-lg font-semibold">{{ __('messages.dashboard.metrics_anomalies') }}</h3>
        <p class="text-xs sm:text-sm mt-1" style="color: rgba(255,255,255,0.8);">{{ __('messages.dashboard.metrics_normal', ['count' => $normalCount]) }}</p>
    </div>
</div>
