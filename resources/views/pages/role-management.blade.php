<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-8">
                <h2 class="font-bold text-xl text-emerald-900">
                    Role & Permission
                </h2>
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 rounded-full border border-emerald-200">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700">Terhubung</span>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-200">
                    <span class="font-semibold" id="currentTime">{{ now()->format('d M Y, H:i:s') }}</span>
                </div>
                <button id="openRoleModal" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold shadow-sm">
                    <span class="text-lg leading-none">+</span>
                    Tambah Role
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-4">
                <div class="text-sm font-semibold text-gray-700 mb-2">Cari Role</div>
                <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-3 py-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                    </svg>
                    <input id="roleSearchInput" type="text" placeholder="Ketik nama role..." class="w-full bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none" />
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-emerald-50 text-emerald-800">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">ROLE NAME</th>
                                <th class="px-6 py-3 text-left font-semibold">PERMISSIONS</th>
                                <th class="px-6 py-3 text-left font-semibold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="roleTableBody">
                            @forelse($roles as $role)
                                @php
                                    $perms = $role->permissions ?? [];
                                    $permList = ($perms === ['*']) ? ['Full Access'] : $perms;
                                    $showPerms = array_slice($permList, 0, 5);
                                    $extraCount = max(count($permList) - count($showPerms), 0);
                                @endphp
                                <tr data-role="{{ strtolower($role->name) }}">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $role->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $role->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($showPerms as $perm)
                                                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
                                                    {{ $permissionLabels[$perm] ?? $perm }}
                                                </span>
                                            @endforeach
                                            @if($extraCount > 0)
                                                <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">+{{ $extraCount }} lainnya</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($role->is_protected)
                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-semibold">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM5 21h14a2 2 0 002-2v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1a2 2 0 002 2z" />
                                                </svg>
                                                Protected Role
                                            </span>
                                        @else
                                            <div class="flex items-center gap-4 text-sm">
                                                <button class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700 btn-edit-role"
                                                    data-role="{{ $role->id }}"
                                                    data-name="{{ $role->name }}"
                                                    data-slug="{{ $role->slug }}"
                                                    data-permissions='@json($role->permissions ?? [])'>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-1-1v2m-6 4h12M5 12h14M7 16h10" />
                                                    </svg>
                                                    Edit
                                                </button>
                                                <button class="inline-flex items-center gap-1 text-red-500 hover:text-red-600 btn-delete-role"
                                                    data-role="{{ $role->id }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V4h6v3m-7 4v7m4-7v7m4-7v7M5 7h14l-1 14H6L5 7z" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-6 text-center text-gray-400">Belum ada role.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="roleModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30 backdrop-blur-sm p-4">
        <div class="w-full max-w-lg bg-white rounded-xl shadow-lg max-h-[85vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-4 py-3 border-b flex-shrink-0" style="background: #047857; border-color: #047857;">
                <h3 class="text-base font-bold text-white">Tambah Role Baru</h3>
                <button id="closeRoleModal" class="text-emerald-100 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="roleForm" class="p-4 space-y-3 overflow-y-auto">
                <input type="hidden" id="roleId" />
                <div>
                    <input id="roleName" type="text" placeholder="Contoh: Admin" class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-semibold text-gray-700">Permissions</label>
                        <span id="permCount" class="text-xs text-gray-500">0 dipilih</span>
                    </div>
                    <div class="mt-2 space-y-2 max-h-64 overflow-y-auto pr-2">
                        @foreach($permissionGroups as $groupName => $perms)
                            <div class="rounded-lg border border-gray-200 p-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center">🔹</div>
                                        <div>
                                            <div class="font-semibold text-gray-900 text-sm">{{ $groupName }}</div>
                                            <div class="text-[11px] text-gray-500">Akses modul {{ strtolower($groupName) }}</div>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ count($perms) }} item</span>
                                </div>
                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700">
                                    @foreach($perms as $key => $label)
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" class="perm-checkbox rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" value="{{ $key }}" />
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
            <div class="px-4 py-3 border-t border-gray-100 flex justify-end gap-2 flex-shrink-0">
                <button id="cancelRoleModal" class="px-3 py-2 rounded-md bg-gray-100 text-gray-700 text-sm font-semibold">Batal</button>
                <button id="saveRoleBtn" class="px-3 py-2 rounded-md bg-emerald-700 hover:bg-emerald-800 text-white text-sm font-semibold">Simpan</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const openBtn = document.getElementById('openRoleModal');
                const closeBtn = document.getElementById('closeRoleModal');
                const cancelBtn = document.getElementById('cancelRoleModal');
                const modal = document.getElementById('roleModal');
                const input = document.getElementById('roleSearchInput');
                const rows = Array.from(document.querySelectorAll('#roleTableBody tr'));
                const roleForm = document.getElementById('roleForm');
                const roleId = document.getElementById('roleId');
                const roleName = document.getElementById('roleName');
                const permCount = document.getElementById('permCount');
                const permBoxes = () => Array.from(document.querySelectorAll('.perm-checkbox'));
                const saveBtn = document.getElementById('saveRoleBtn');
                const editButtons = Array.from(document.querySelectorAll('.btn-edit-role'));
                const deleteButtons = Array.from(document.querySelectorAll('.btn-delete-role'));

                const updatePermCount = () => {
                    const count = permBoxes().filter(b => b.checked).length;
                    permCount.textContent = `${count} dipilih`;
                };
                permBoxes().forEach(b => b.addEventListener('change', updatePermCount));

                function openModal() {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
                function resetForm() {
                    roleId.value = '';
                    roleName.value = '';
                    permBoxes().forEach(b => { b.checked = false; });
                    updatePermCount();
                }

                openBtn?.addEventListener('click', openModal);
                closeBtn?.addEventListener('click', () => { closeModal(); resetForm(); });
                cancelBtn?.addEventListener('click', () => { closeModal(); resetForm(); });
                modal?.addEventListener('click', function (e) {
                    if (e.target === modal) { closeModal(); resetForm(); }
                });

                input?.addEventListener('input', function () {
                    const q = input.value.trim().toLowerCase();
                    rows.forEach(row => {
                        const role = row.getAttribute('data-role') || '';
                        row.classList.toggle('hidden', q && !role.includes(q));
                    });
                });

                editButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        resetForm();
                        roleId.value = btn.dataset.role || '';
                        roleName.value = btn.dataset.name || '';
                        const perms = JSON.parse(btn.dataset.permissions || '[]');
                        permBoxes().forEach(b => { b.checked = perms.includes(b.value); });
                        updatePermCount();
                        openModal();
                    });
                });

                deleteButtons.forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const id = btn.dataset.role;
                        if (!id || !confirm('Hapus role ini?')) return;
                        const res = await fetch(`/role-management/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        const data = await res.json();
                        if (data.success) window.location.reload();
                        else alert(data.message || 'Gagal menghapus role.');
                    });
                });

                saveBtn?.addEventListener('click', async () => {
                    const name = roleName.value.trim();
                    if (!name) {
                        alert('Nama role wajib diisi.');
                        return;
                    }
                    const permissions = permBoxes().filter(b => b.checked).map(b => b.value);
                    const payload = { name, permissions };
                    const isEdit = !!roleId.value;
                    const url = isEdit ? `/role-management/${roleId.value}` : '/role-management';
                    const method = isEdit ? 'PUT' : 'POST';
                    const res = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (data.success) window.location.reload();
                    else alert(data.message || 'Gagal menyimpan role.');
                });
            });
        </script>
    @endpush
</x-app-layout>
