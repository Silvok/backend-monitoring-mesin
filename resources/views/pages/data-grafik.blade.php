<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <h2 class="font-bold text-xl text-emerald-900 leading-tight">
                    Monitoring Mesin
                </h2>
                <!-- Live Status Indicator -->
                <div
                    class="flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 rounded-full border border-emerald-200">
                    <div class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700">Live</span>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div
                    class="hidden sm:block text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold"
                        id="currentTime">{{ now()->locale('id')->translatedFormat('l, d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
        <script>
            function startClock() {
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                function updateTime() {
                    const now = new Date();
                    const dayName = days[now.getDay()];
                    const day = String(now.getDate()).padStart(2, '0');
                    const month = months[now.getMonth()];
                    const year = now.getFullYear();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');

                    const timeString = `${dayName}, ${day} ${month} ${year}, ${hours}:${minutes}`;
                    const timeElement = document.getElementById('currentTime');
                    if (timeElement) {
                        timeElement.textContent = timeString;
                    }
                }

                updateTime(); // Update immediately
                setInterval(updateTime, 60000); // Update every minute
            }

            document.addEventListener('DOMContentLoaded', startClock);
        </script>
    @endpush

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

            </div>
        </div>
    </div>
</x-app-layout>