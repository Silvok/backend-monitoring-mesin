<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl text-emerald-900">{{ __('messages.settings.title') }}</h2>
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 rounded-full border border-emerald-200">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700">{{ __('messages.app.active') }}</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold" id="currentTime">{{ now()->format('d M Y, H:i:s') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 rounded-2xl border border-gray-100 bg-white shadow-sm p-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h10M4 18h6" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">{{ __('messages.settings.general') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('messages.settings.general_desc') }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3 rounded-xl border border-gray-100 bg-gray-50">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.timezone') }}</p>
                            <p class="mt-2 text-sm text-gray-700">{{ config('app.timezone') }}</p>
                        </div>
                        <div class="p-3 rounded-xl border border-gray-100 bg-gray-50">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.language') }}</p>
                            <p class="mt-2 text-sm text-gray-700">Indonesia</p>
                        </div>
                        <div class="p-3 rounded-xl border border-gray-100 bg-gray-50">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.auto_refresh') }}</p>
                            <p class="mt-2 text-sm text-gray-700">30 detik</p>
                        </div>
                        <div class="p-3 rounded-xl border border-gray-100 bg-gray-50">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.auto_zoom') }}</p>
                            <p class="mt-2 text-sm text-gray-700">{{ __('messages.app.active') }}</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-yellow-100 text-yellow-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">{{ __('messages.settings.system_info') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('messages.settings.system_status') }}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="p-3 rounded-xl border border-gray-100 bg-gray-50">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Environment</p>
                            <p class="mt-2 text-sm text-gray-700">{{ config('app.env') }}</p>
                        </div>
                        <div class="p-3 rounded-xl border border-gray-100 bg-gray-50">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.queue_driver') }}</p>
                            <p class="mt-2 text-sm text-gray-700">{{ strtoupper($queueDriver) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex flex-wrap items-center gap-2 px-4 pt-4">
                    <button class="settings-tab px-3 py-1.5 rounded-full text-[11px] font-bold bg-emerald-600 text-white" data-tab="notif">{{ __('messages.settings.notifications') }}</button>
                    <button class="settings-tab px-3 py-1.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600" data-tab="machine">{{ __('messages.settings.machine') }}</button>
                    <button class="settings-tab px-3 py-1.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600" data-tab="sampling">{{ __('messages.settings.sampling') }}</button>
                    <button class="settings-tab px-3 py-1.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600" data-tab="akun">{{ __('messages.settings.account') }}</button>
                </div>

                <div class="px-4 pb-4 pt-3 space-y-4">
                    <div id="tab-notif" class="settings-panel">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 1.343-3 3v4a3 3 0 006 0v-4c0-1.657-1.343-3-3-3zm-7 3a7 7 0 0114 0v4a7 7 0 01-14 0v-4z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-gray-900">{{ __('messages.settings.notifications') }}</h3>
                                            <p class="text-xs text-gray-500">{{ __('messages.settings.notifications_desc') }}</p>
                                        </div>
                                    </div>
                                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">Web</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.notif_min_level') }}</p>
                                        <div class="mt-3 flex items-center gap-2">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">WARNING</span>
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">CRITICAL</span>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.notif_refresh') }}</p>
                                        <p class="mt-2 text-sm text-gray-700">30 detik</p>
                                    </div>
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.notif_unread') }}</p>
                                        <p class="mt-2 text-sm text-gray-700">{{ $unreadCount }} notifikasi</p>
                                    </div>
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.notif_sound') }}</p>
                                        <p class="mt-2 text-sm text-gray-700">Aktif untuk alert kritis</p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-9 h-9 rounded-xl bg-yellow-100 text-yellow-700 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">{{ __('messages.settings.quick_actions') }}</h3>
                                        <p class="text-xs text-gray-500">{{ __('messages.settings.quick_actions_desc') }}</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700">{{ __('messages.settings.alert_management_hint') }}</p>
                                <a href="{{ route('alert-management') }}"
                                    class="mt-4 inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow">
                                    {{ __('messages.settings.open_alert_management') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div id="tab-machine" class="settings-panel hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="lg:col-span-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">{{ __('messages.settings.machine_thresholds') }}</h3>
                                        <p class="text-xs text-gray-500">{{ __('messages.settings.machine_thresholds_desc') }}</p>
                                    </div>
                                </div>
                                <form id="machineThresholdForm" class="space-y-4">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.select_machine') }}</label>
                                        <select id="machineSelect" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                            @foreach($machines as $machine)
                                                <option value="{{ $machine->id }}"
                                                    data-warning="{{ $machine->threshold_warning ?? 21.84 }}"
                                                    data-critical="{{ $machine->threshold_critical ?? 25.11 }}"
                                                    data-hp="{{ $machine->motor_power_hp ?? '' }}"
                                                    data-rpm="{{ $machine->motor_rpm ?? '' }}"
                                                    data-iso="{{ $machine->iso_class ?? 'Class II' }}">
                                                    {{ $machine->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.warning') }}</label>
                                            <input id="machineWarning" type="number" step="0.01" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.critical') }}</label>
                                            <input id="machineCritical" type="number" step="0.01" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.motor_power') }}</label>
                                            <input id="machineHp" type="number" step="0.1" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.motor_rpm') }}</label>
                                            <input id="machineRpm" type="number" step="1" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Kategori Mesin</label>
                                        <select id="machineIso" class="mt-2 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                            <option value="Class I">Class I</option>
                                            <option value="Class II">Class II</option>
                                            <option value="Class III">Class III</option>
                                            <option value="Class IV">Class IV</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow">{{ __('messages.settings.save_threshold') }}</button>
                                        <a href="{{ route('alert-management') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                                            {{ __('messages.settings.manage_thresholds') }}
                                        </a>
                                    </div>
                                    <p id="thresholdMessage" class="text-xs text-gray-500"></p>
                                </form>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <h3 class="text-base font-bold text-gray-900 mb-3">{{ __('messages.settings.summary') }}</h3>
                                <p class="text-sm text-gray-700">{{ __('messages.settings.summary_hint') }}</p>
                                <div class="mt-4 space-y-3">
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Total {{ __('messages.settings.machine') }}</p>
                                        <p class="mt-2 text-sm text-gray-700">{{ $machines->count() }}</p>
                                    </div>
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.default_warning') }}</p>
                                        <p class="mt-2 text-sm text-gray-700">21.84 mm/s</p>
                                    </div>
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.default_critical') }}</p>
                                        <p class="mt-2 text-sm text-gray-700">25.11 mm/s</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tab-sampling" class="settings-panel hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="lg:col-span-2 rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-9 h-9 rounded-xl bg-yellow-100 text-yellow-700 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">{{ __('messages.settings.sampling') }} & Batch</h3>
                                        <p class="text-xs text-gray-500">{{ __('messages.settings.sampling_desc') }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.sample_rate') }}</p>
                                        <p class="mt-2 text-lg font-bold text-gray-900">{{ $sampleRate }} Hz</p>
                                    </div>
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.batch_size') }}</p>
                                        <p class="mt-2 text-lg font-bold text-gray-900">{{ $batchSize }}</p>
                                    </div>
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.queue_driver') }}</p>
                                        <p class="mt-2 text-lg font-bold text-gray-900">{{ strtoupper($queueDriver) }}</p>
                                    </div>
                                </div>
                                <div class="mt-4 p-3 rounded-xl border border-gray-100 bg-white">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Interval Simpan Data</p>
                                    <div class="mt-2 flex flex-wrap items-center gap-3">
                                        <select id="samplingIntervalSelect" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                            @foreach([1, 5, 10] as $opt)
                                                <option value="{{ $opt }}" @if($samplingIntervalMinutes == $opt) selected @endif>
                                                    {{ $opt }} menit
                                                </option>
                                            @endforeach
                                        </select>
                                        <button id="saveSamplingInterval" type="button" class="px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow">
                                            Simpan
                                        </button>
                                        <span id="samplingIntervalMsg" class="text-xs text-gray-500"></span>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">Hanya 1 data ringkasan per interval disimpan ke database.</p>
                                </div>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <h3 class="text-base font-bold text-gray-900 mb-3">{{ __('messages.settings.notes') }}</h3>
                                <p class="text-sm text-gray-700">Ubah sample rate di perangkat dan pastikan .env sesuai.</p>
                                <div class="mt-4 space-y-3">
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.env_key') }}</p>
                                        <p class="mt-2 text-sm text-gray-700">SAMPLE_RATE_HZ</p>
                                    </div>
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.batch_key') }}</p>
                                        <p class="mt-2 text-sm text-gray-700">N_SAMPLES</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tab-akun" class="settings-panel hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-9 h-9 rounded-xl bg-yellow-100 text-yellow-700 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 5.636l-1.414 1.414A2 2 0 0116 7.172V7a2 2 0 00-2-2h-.172a2 2 0 01-1.414-.586L11 3m0 0L9.586 4.414A2 2 0 018.172 5H8a2 2 0 00-2 2v.172a2 2 0 01-.586 1.414L4 11m0 0l1.414 1.414A2 2 0 015 13.828V14a2 2 0 002 2h.172a2 2 0 011.414.586L11 17m0 0l1.414-1.414A2 2 0 0113.828 15H14a2 2 0 002-2v-.172a2 2 0 01.586-1.414L18 11m0 0l-1.414-1.414A2 2 0 0116 7.172V7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">{{ __('messages.settings.account') }} & Keamanan</h3>
                                        <p class="text-xs text-gray-500">{{ __('messages.settings.account_desc') }}</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.default_role') }}</p>
                                        <p class="mt-2 text-sm text-gray-700">Teknisi</p>
                                    </div>
                                    <div class="p-3 rounded-xl border border-gray-100 bg-white">
                                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.settings.password_policy') }}</p>
                                        <p class="mt-2 text-sm text-gray-700">Minimal 8 karakter</p>
                                    </div>
                                </div>
                                <a href="{{ route('user-management') }}" class="mt-4 inline-flex items-center px-3 py-1.5 rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold shadow">
                                    {{ __('messages.settings.open_user_management') }}
                                </a>
                            </div>
                            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                                <h3 class="text-base font-bold text-gray-900 mb-3">{{ __('messages.settings.security_tips') }}</h3>
                                <p class="text-sm text-gray-700">Gunakan role sesuai tugas dan aktifkan audit login.</p>
                                <ul class="mt-4 text-sm text-gray-600 list-disc list-inside">
                                    <li>{{ __('messages.settings.tip_disable_user') }}</li>
                                    <li>{{ __('messages.settings.tip_reset_password') }}</li>
                                    <li>{{ __('messages.settings.tip_use_email') }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4 rounded-2xl border border-gray-100 bg-white shadow-sm">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <h3 class="text-base font-bold text-gray-900">Role & Hak Akses</h3>
                                <p class="text-xs text-gray-500">Ringkasan hak akses per role sesuai kebijakan sistem.</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-emerald-700 text-white">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold">Role</th>
                                            <th class="px-4 py-3 text-left font-semibold">Permissions</th>
                                            <th class="px-4 py-3 text-left font-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr class="bg-white">
                                            <td class="px-4 py-4">
                                                <div class="font-semibold text-gray-900">Super Admin</div>
                                                <div class="text-xs text-gray-500">super_admin</div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Dashboard</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Alert & Acknowledge</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Histori & Tabel</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Export</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Manajemen User</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Konfigurasi Sistem</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-semibold">
                                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM5 21h14a2 2 0 002-2v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1a2 2 0 002 2z" />
                                                    </svg>
                                                    Protected Role
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="bg-white">
                                            <td class="px-4 py-4">
                                                <div class="font-semibold text-gray-900">Admin</div>
                                                <div class="text-xs text-gray-500">admin</div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Dashboard</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Alert & Acknowledge</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Histori & Tabel</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Export</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Manajemen User (Terbatas)</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-3 text-emerald-600 text-sm">
                                                    <span class="inline-flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 4h12M5 12h14M7 16h10" />
                                                        </svg>
                                                        Edit
                                                    </span>
                                                    <span class="inline-flex items-center gap-1 text-red-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V4h6v3m-7 4v7m4-7v7m4-7v7M5 7h14l-1 14H6L5 7z" />
                                                        </svg>
                                                        Delete
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="bg-white">
                                            <td class="px-4 py-4">
                                                <div class="font-semibold text-gray-900">Koordinator</div>
                                                <div class="text-xs text-gray-500">koordinator</div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex flex-wrap gap-2">
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Dashboard</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Grafik Real-time</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Histori (Lihat)</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Alert (Lihat)</span>
                                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">Acknowledge</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex items-center gap-3 text-emerald-600 text-sm">
                                                    <span class="inline-flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 4h12M5 12h14M7 16h10" />
                                                        </svg>
                                                        Edit
                                                    </span>
                                                    <span class="inline-flex items-center gap-1 text-red-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V4h6v3m-7 4v7m4-7v7m4-7v7M5 7h14l-1 14H6L5 7z" />
                                                        </svg>
                                                        Delete
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = Array.from(document.querySelectorAll('.settings-tab'));
            const panels = Array.from(document.querySelectorAll('.settings-panel'));
            const machineSelect = document.getElementById('machineSelect');
            const warningInput = document.getElementById('machineWarning');
            const criticalInput = document.getElementById('machineCritical');
            const hpInput = document.getElementById('machineHp');
            const rpmInput = document.getElementById('machineRpm');
            const isoSelect = document.getElementById('machineIso');
            const form = document.getElementById('machineThresholdForm');
            const msg = document.getElementById('thresholdMessage');
            const samplingSelect = document.getElementById('samplingIntervalSelect');
            const samplingSaveBtn = document.getElementById('saveSamplingInterval');
            const samplingMsg = document.getElementById('samplingIntervalMsg');

            function switchTab(tabId) {
                tabs.forEach(tab => {
                    const active = tab.getAttribute('data-tab') === tabId;
                    tab.classList.toggle('bg-emerald-600', active);
                    tab.classList.toggle('text-white', active);
                    tab.classList.toggle('bg-gray-100', !active);
                    tab.classList.toggle('text-gray-600', !active);
                });
                panels.forEach(panel => {
                    panel.classList.toggle('hidden', panel.id !== `tab-${tabId}`);
                });
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    switchTab(tab.getAttribute('data-tab'));
                });
            });

            function loadMachineValues() {
                if (!machineSelect) return;
                const option = machineSelect.options[machineSelect.selectedIndex];
                if (!option) return;
                warningInput.value = option.getAttribute('data-warning') || 21.84;
                criticalInput.value = option.getAttribute('data-critical') || 25.11;
                hpInput.value = option.getAttribute('data-hp') || '';
                rpmInput.value = option.getAttribute('data-rpm') || '';
                isoSelect.value = option.getAttribute('data-iso') || 'Class II';
            }

            if (machineSelect) {
                machineSelect.addEventListener('change', loadMachineValues);
                loadMachineValues();
            }

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    msg.textContent = 'Menyimpan...';
                    const payload = {
                        machine_id: machineSelect.value,
                        warning: warningInput.value,
                        critical: criticalInput.value,
                        motor_power_hp: hpInput.value || null,
                        motor_rpm: rpmInput.value || null,
                        iso_class: isoSelect.value || null
                    };

                    fetch('/api/alert-management/thresholds', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                msg.textContent = data.message || 'Berhasil disimpan.';
                                msg.className = 'text-xs text-emerald-600';
                                const option = machineSelect.options[machineSelect.selectedIndex];
                                option.setAttribute('data-warning', payload.warning);
                                option.setAttribute('data-critical', payload.critical);
                                option.setAttribute('data-hp', payload.motor_power_hp || '');
                                option.setAttribute('data-rpm', payload.motor_rpm || '');
                                option.setAttribute('data-iso', payload.iso_class || '');
                            } else {
                                msg.textContent = data.message || 'Gagal menyimpan.';
                                msg.className = 'text-xs text-red-600';
                            }
                        })
                        .catch(() => {
                            msg.textContent = 'Gagal menyimpan.';
                            msg.className = 'text-xs text-red-600';
                        });
                });
            }

            if (samplingSaveBtn && samplingSelect) {
                samplingSaveBtn.addEventListener('click', function () {
                    samplingMsg.textContent = 'Menyimpan...';
                    fetch('/api/settings/sampling-interval', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            sampling_interval_minutes: parseInt(samplingSelect.value, 10)
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                samplingMsg.textContent = 'Tersimpan.';
                                samplingMsg.className = 'text-xs text-emerald-600';
                            } else {
                                samplingMsg.textContent = data.message || 'Gagal menyimpan.';
                                samplingMsg.className = 'text-xs text-red-600';
                            }
                        })
                        .catch(() => {
                            samplingMsg.textContent = 'Gagal menyimpan.';
                            samplingMsg.className = 'text-xs text-red-600';
                        });
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
