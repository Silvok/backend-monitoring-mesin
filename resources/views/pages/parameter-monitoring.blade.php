<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl text-emerald-900">{{ __('messages.parameters.title') }}</h2>
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
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                    <div class="lg:max-w-sm">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('messages.parameters.subtitle') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('messages.parameters.subtitle_desc') }}</p>
                    </div>
                    <div class="flex-1">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                            <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.parameters.filter_group') }}</label>
                        <select id="paramGroup" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">{{ __('messages.parameters.filter_all_groups') }}</option>
                            @foreach($groups as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                            </div>
                            <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.parameters.filter_status') }}</label>
                        <select id="paramStatus" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">{{ __('messages.parameters.filter_all_status') }}</option>
                            <option value="NORMAL">Normal</option>
                            <option value="WARNING">Warning</option>
                            <option value="CRITICAL">Critical</option>
                            <option value="INFO">Info</option>
                        </select>
                            </div>
                            <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-widest">{{ __('messages.parameters.filter_search') }}</label>
                        <div class="mt-2 flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 h-10">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"></path>
                            </svg>
                            <input id="paramSearch" type="text" placeholder="{{ __('messages.parameters.filter_search_placeholder') }}" class="w-full bg-transparent text-sm text-gray-700 placeholder-gray-500 outline-none h-10 rounded-md border border-gray-300">
                        </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-emerald-600 text-xs uppercase tracking-wider">
                            @php
                                $firstGroupKey = array_key_first($groups);
                                $firstGroupLabel = $groups[$firstGroupKey] ?? '';
                                $firstGroupCount = $groupCounts[$firstGroupKey] ?? 0;
                            @endphp
                            <tr class="bg-emerald-50/60">
                                <th class="px-6 py-4 font-semibold text-gray-900" colspan="5">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                            {{ strtoupper(substr($firstGroupLabel, 0, 1)) }}
                                        </span>
                                        <span class="text-sm font-bold">{{ $firstGroupLabel }}</span>
                                        <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-white border border-emerald-200 text-emerald-700">
                                            {{ $firstGroupCount }} {{ __('messages.parameters.table_params') }}
                                        </span>
                                    </div>
                                </th>
                            </tr>
                            <tr class="bg-gray-200">
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-medium text-gray-700 leading-tight">Nama parameter</span>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-medium text-gray-700 leading-tight">Makna singkat</span>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-medium text-gray-700 leading-tight">Batas / rumus</span>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-medium text-gray-700 leading-tight">Kategori kondisi</span>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-800 uppercase tracking-wider">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-medium text-gray-700 leading-tight">Lihat detail</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="paramTableBody" class="divide-y divide-gray-100">
                            @foreach($groups as $groupKey => $groupLabel)
                                @if($groupKey !== $firstGroupKey)
                                    <tr class="group-row bg-emerald-50/60" data-group="{{ $groupKey }}">
                                        <td class="px-6 py-4 font-semibold text-gray-900" colspan="5">
                                            <div class="flex items-center gap-3">
                                                <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                                    {{ strtoupper(substr($groupLabel, 0, 1)) }}
                                                </span>
                                                <span class="text-sm font-bold">{{ $groupLabel }}</span>
                                                <span class="text-[10px] font-semibold px-2 py-1 rounded-full bg-white border border-emerald-200 text-emerald-700">
                                                    {{ $groupCounts[$groupKey] ?? 0 }} {{ __('messages.parameters.table_params') }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @foreach($parameters as $item)
                                    @if($item['group'] === $groupKey)
                                        <tr class="param-row" data-group="{{ $item['group'] }}" data-status="{{ $item['status'] }}" data-search="{{ strtolower($item['label'].' '.$item['description'].' '.$item['value']) }}">
                                            <td class="px-6 py-4 font-semibold text-gray-900">
                                                <div class="flex flex-col">
                                                    <span>{{ $item['label'] }}</span>
                                                    <span class="text-[11px] text-gray-400">{{ $item['key'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600">{{ $item['description'] }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    {{ $item['value'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $statusClasses = [
                                                        'NORMAL' => 'bg-emerald-100 text-emerald-700',
                                                        'WARNING' => 'bg-yellow-100 text-yellow-700',
                                                        'CRITICAL' => 'bg-red-100 text-red-700',
                                                        'INFO' => 'bg-blue-100 text-blue-700',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$item['status']] ?? 'bg-gray-100 text-gray-700' }}">
                                                    {{ $item['status'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <button type="button" class="param-detail-btn text-emerald-700 hover:text-emerald-800 text-xs font-semibold"
                                                    data-title="{{ $item['label'] }}"
                                                    data-detail="{{ $item['detail'] }}">
                                                    {{ __('messages.parameters.table_detail') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 px-4 py-3 text-xs text-emerald-700">
                {{ __('messages.parameters.note') }}
            </div>
        </div>
    </div>

    <div id="paramDetailBackdrop" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-gray-100 mx-4">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                <div>
                    <p class="text-sm font-bold text-gray-900" id="paramDetailTitle">Detail</p>
                    <p class="text-[11px] text-gray-500">Ringkas dan praktis</p>
                </div>
                <button id="paramDetailClose" type="button" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="px-4 py-4">
                <p class="text-sm text-gray-700" id="paramDetailBody"></p>
            </div>
            <div class="px-4 py-3 border-t border-gray-100 flex justify-end">
                <button id="paramDetailOk" type="button" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">
                    OK
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const groupSelect = document.getElementById('paramGroup');
                const statusSelect = document.getElementById('paramStatus');
                const searchInput = document.getElementById('paramSearch');
                const rows = Array.from(document.querySelectorAll('.param-row'));
                const groupHeaders = Array.from(document.querySelectorAll('tr.group-row'));
                const detailButtons = Array.from(document.querySelectorAll('.param-detail-btn'));
                const detailBackdrop = document.getElementById('paramDetailBackdrop');
                const detailTitle = document.getElementById('paramDetailTitle');
                const detailBody = document.getElementById('paramDetailBody');
                const detailClose = document.getElementById('paramDetailClose');
                const detailOk = document.getElementById('paramDetailOk');

                function applyFilters() {
                    const group = groupSelect.value;
                    const status = statusSelect.value;
                    const query = searchInput.value.trim().toLowerCase();

                    rows.forEach(row => {
                        const matchesGroup = !group || row.dataset.group === group;
                        const matchesStatus = !status || row.dataset.status === status;
                        const matchesQuery = !query || row.dataset.search.includes(query);
                        row.classList.toggle('hidden', !(matchesGroup && matchesStatus && matchesQuery));
                    });

                    groupHeaders.forEach(header => {
                        const groupKey = header.dataset.group;
                        const hasVisible = rows.some(row => row.dataset.group === groupKey && !row.classList.contains('hidden'));
                        header.classList.toggle('hidden', !hasVisible);
                    });
                }

                [groupSelect, statusSelect, searchInput].forEach(input => {
                    input.addEventListener('change', applyFilters);
                    input.addEventListener('keyup', applyFilters);
                });

                detailButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        detailTitle.textContent = button.getAttribute('data-title') || 'Detail';
                        detailBody.textContent = button.getAttribute('data-detail') || '-';
                        detailBackdrop.classList.remove('hidden');
                        detailBackdrop.classList.add('flex');
                    });
                });

                function closeDetail() {
                    detailBackdrop.classList.add('hidden');
                    detailBackdrop.classList.remove('flex');
                }

                detailClose.addEventListener('click', closeDetail);
                detailOk.addEventListener('click', closeDetail);
                detailBackdrop.addEventListener('click', (event) => {
                    if (event.target === detailBackdrop) {
                        closeDetail();
                    }
                });

                applyFilters();
            });
        </script>
    @endpush
</x-app-layout>
