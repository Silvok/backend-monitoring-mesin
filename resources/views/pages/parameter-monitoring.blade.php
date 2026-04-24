<x-app-layout>
    <x-slot name="header">
        <div class="w-full min-w-0 flex items-center justify-between gap-2">
            <div class="min-w-0 flex-1">
                <h2 class="font-bold text-base sm:text-xl text-emerald-900 truncate">
                    {{ __('messages.parameters.title') }}
                </h2>
            </div>
            <div class="flex-shrink-0">
                <div class="inline-flex items-center text-[10px] sm:text-sm text-gray-600 bg-gray-50 px-2 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold whitespace-nowrap tabular-nums" id="currentTime">{{ now()->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
                    <div>
                        <label for="paramGroup" class="sr-only">Group</label>
                        <select id="paramGroup" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Groups</option>
                            @foreach($groups as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="paramStatus" class="sr-only">Status</label>
                        <select id="paramStatus" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All Status</option>
                            <option value="NORMAL">Normal</option>
                            <option value="WARNING">Warning</option>
                            <option value="CRITICAL">Critical</option>
                            <option value="INFO">Info</option>
                        </select>
                    </div>
                    <div>
                        <label for="paramSearch" class="sr-only">Search</label>
                        <div class="flex flex-wrap sm:flex-nowrap gap-2">
                            <input id="paramSearch" type="text" placeholder="Search parameters..." class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                            <button id="paramReloadBtn" type="button" class="px-3 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold transition">
                                Reload
                            </button>
                            <button id="paramClearBtn" type="button" class="px-3 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold transition">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[720px] w-full text-sm text-left text-gray-700">
                        <thead class="bg-emerald-700 text-white">
                            <tr>
                                <th class="px-3 py-3 sm:px-6 sm:py-4 text-xs font-bold uppercase tracking-wider">Parameter Key</th>
                                <th class="px-3 py-3 sm:px-6 sm:py-4 text-xs font-bold uppercase tracking-wider">Description</th>
                                <th class="px-3 py-3 sm:px-6 sm:py-4 text-xs font-bold uppercase tracking-wider">Value</th>
                                <th class="px-3 py-3 sm:px-6 sm:py-4 text-xs font-bold uppercase tracking-wider">Status</th>
                                <th class="px-3 py-3 sm:px-6 sm:py-4 text-xs font-bold uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="paramTableBody" class="divide-y divide-gray-100">
                            @foreach($groups as $groupKey => $groupLabel)
                                <tr class="group-row bg-emerald-50/60" data-group="{{ $groupKey }}">
                                    <td colspan="5" class="px-3 py-3 sm:px-6 sm:py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                                                {{ strtoupper(substr($groupLabel, 0, 1)) }}
                                            </span>
                                            <span class="text-sm sm:text-base font-bold text-gray-900">{{ strtoupper($groupLabel) }}</span>
                                            <span class="px-2.5 sm:px-3 py-1 rounded-full bg-white border border-gray-300 text-[11px] sm:text-xs font-semibold text-gray-700">
                                                {{ $groupCounts[$groupKey] ?? 0 }} parameters
                                            </span>
                                        </div>
                                    </td>
                                </tr>

                                @foreach($parameters as $item)
                                    @if($item['group'] === $groupKey)
                                        @php
                                            $statusClasses = [
                                                'NORMAL' => 'bg-emerald-100 text-emerald-700',
                                                'WARNING' => 'bg-yellow-100 text-yellow-700',
                                                'CRITICAL' => 'bg-red-100 text-red-700',
                                                'INFO' => 'bg-blue-100 text-blue-700',
                                            ];
                                        @endphp
                                        <tr
                                            class="param-row"
                                            data-group="{{ $item['group'] }}"
                                            data-key="{{ $item['key'] }}"
                                            data-group-label="{{ $groups[$item['group']] ?? $item['group'] }}"
                                            data-status="{{ $item['status'] }}"
                                            data-value="{{ $item['value'] }}"
                                            data-description="{{ $item['description'] }}"
                                            data-search="{{ strtolower($item['key'].' '.$item['description'].' '.$item['value'].' '.$item['status']) }}"
                                        >
                                            <td class="px-3 py-3 sm:px-6 sm:py-4 font-bold text-gray-900">{{ $item['key'] }}</td>
                                            <td class="px-3 py-3 sm:px-6 sm:py-4 text-gray-700 param-desc-text">{{ $item['description'] }}</td>
                                            <td class="px-3 py-3 sm:px-6 sm:py-4">
                                                <span class="inline-flex items-center px-2.5 sm:px-3 py-1 rounded-xl text-xs sm:text-sm font-semibold bg-gray-100 text-gray-800 param-value-text">
                                                    {{ $item['value'] }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3 sm:px-6 sm:py-4">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold param-status-badge {{ $statusClasses[$item['status']] ?? 'bg-gray-100 text-gray-700' }}">
                                                    <span class="param-status-text">{{ $item['status'] }}</span>
                                                </span>
                                            </td>
                                            <td class="px-3 py-3 sm:px-6 sm:py-4">
                                                <button type="button" class="param-edit-btn inline-flex items-center gap-2 text-emerald-700 hover:text-emerald-800 text-sm font-semibold">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 4h12M5 12h14M7 16h10" />
                                                    </svg>
                                                    <span class="hidden sm:inline">Edit</span>
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
        </div>
    </div>

    <div id="paramEditBackdrop" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-xl border border-gray-100 mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-xl sm:text-2xl font-bold text-gray-900">Edit Parameter</h3>
                <button id="paramEditClose" type="button" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="paramEditForm" class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-gray-700 block mb-2">Parameter Key</label>
                    <p id="editParamKey" class="inline-block px-2 py-1 bg-gray-100 text-gray-900 text-xl"></p>
                </div>
                <div>
                    <label for="editParamValue" class="text-sm font-semibold text-gray-700 block mb-2">Value <span class="text-red-500">*</span></label>
                    <textarea id="editParamValue" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>
                <div>
                    <label for="editParamDescription" class="text-sm font-semibold text-gray-700 block mb-2">Description</label>
                    <textarea id="editParamDescription" rows="3" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                </div>
                <div>
                    <label for="editParamGroup" class="text-sm font-semibold text-gray-700 block mb-2">Group</label>
                    <select id="editParamGroup" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        @foreach($groups as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih group untuk mengelompokkan parameter yang sejenis</p>
                </div>
                <div>
                    <label for="editParamStatus" class="text-sm font-semibold text-gray-700 block mb-2">Status</label>
                    <select id="editParamStatus" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="NORMAL">Normal</option>
                        <option value="WARNING">Warning</option>
                        <option value="CRITICAL">Critical</option>
                        <option value="INFO">Info</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button id="paramEditCancel" type="button" class="px-6 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-semibold">
                        Update Parameter
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const groupSelect = document.getElementById('paramGroup');
                const statusSelect = document.getElementById('paramStatus');
                const searchInput = document.getElementById('paramSearch');
                const clearBtn = document.getElementById('paramClearBtn');
                const reloadBtn = document.getElementById('paramReloadBtn');
                const rows = Array.from(document.querySelectorAll('.param-row'));
                const groupHeaders = Array.from(document.querySelectorAll('.group-row'));
                const editButtons = Array.from(document.querySelectorAll('.param-edit-btn'));

                const editBackdrop = document.getElementById('paramEditBackdrop');
                const editClose = document.getElementById('paramEditClose');
                const editCancel = document.getElementById('paramEditCancel');
                const editForm = document.getElementById('paramEditForm');
                const editKey = document.getElementById('editParamKey');
                const editValue = document.getElementById('editParamValue');
                const editDescription = document.getElementById('editParamDescription');
                const editGroup = document.getElementById('editParamGroup');
                const editStatus = document.getElementById('editParamStatus');

                let currentEditingRow = null;

                function statusClass(status) {
                    const map = {
                        NORMAL: 'bg-emerald-100 text-emerald-700',
                        WARNING: 'bg-yellow-100 text-yellow-700',
                        CRITICAL: 'bg-red-100 text-red-700',
                        INFO: 'bg-blue-100 text-blue-700',
                    };
                    return map[status] || 'bg-gray-100 text-gray-700';
                }

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

                function openEditModal(row) {
                    currentEditingRow = row;
                    editKey.textContent = row.dataset.key || '-';
                    editValue.value = row.dataset.value || '';
                    editDescription.value = row.dataset.description || '';
                    editGroup.value = row.dataset.group || '';
                    editStatus.value = row.dataset.status || 'INFO';
                    editBackdrop.classList.remove('hidden');
                    editBackdrop.classList.add('flex');
                }

                function closeEditModal() {
                    editBackdrop.classList.add('hidden');
                    editBackdrop.classList.remove('flex');
                    currentEditingRow = null;
                    editForm.reset();
                }

                [groupSelect, statusSelect].forEach(el => el.addEventListener('change', applyFilters));
                searchInput.addEventListener('keyup', applyFilters);
                searchInput.addEventListener('change', applyFilters);

                clearBtn.addEventListener('click', function () {
                    groupSelect.value = '';
                    statusSelect.value = '';
                    searchInput.value = '';
                    applyFilters();
                });

                reloadBtn.addEventListener('click', function () {
                    window.location.reload();
                });

                editButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const row = button.closest('.param-row');
                        if (!row) return;
                        openEditModal(row);
                    });
                });

                editClose.addEventListener('click', closeEditModal);
                editCancel.addEventListener('click', closeEditModal);
                editBackdrop.addEventListener('click', function (event) {
                    if (event.target === editBackdrop) closeEditModal();
                });

                editForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    if (!currentEditingRow) return;

                    const newValue = editValue.value.trim();
                    const newDescription = editDescription.value.trim();
                    const newGroup = editGroup.value;
                    const newStatus = editStatus.value;
                    const key = currentEditingRow.dataset.key || '';

                    currentEditingRow.dataset.value = newValue;
                    currentEditingRow.dataset.description = newDescription;
                    currentEditingRow.dataset.group = newGroup;
                    currentEditingRow.dataset.status = newStatus;
                    currentEditingRow.dataset.search = `${key} ${newDescription} ${newValue} ${newStatus}`.toLowerCase();

                    currentEditingRow.querySelector('.param-value-text').textContent = newValue || '-';
                    currentEditingRow.querySelector('.param-desc-text').textContent = newDescription || '-';
                    currentEditingRow.querySelector('.param-status-text').textContent = newStatus;
                    currentEditingRow.querySelector('.param-status-badge').className =
                        `inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold param-status-badge ${statusClass(newStatus)}`;

                    applyFilters();
                    closeEditModal();
                });

                applyFilters();
            });
        </script>
    @endpush
</x-app-layout>
