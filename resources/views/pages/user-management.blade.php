

<x-app-layout>
	@section('title', 'Manajemen User')
	@push('styles')
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	@endpush

	<x-slot name="header">
		<div class="flex items-center justify-between">
			<div class="flex items-center space-x-8">
				<h2 class="font-bold text-xl text-emerald-900">
					Manajemen User
				</h2>
				<!-- Live Status Indicator -->
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
			</div>
		</div>
	</x-slot>

	<div class="py-6 px-4 sm:px-6 lg:px-8">
		<!-- Notifikasi -->
		<div id="toast-container"></div>

		<div class="mb-4">
			<div class="rounded-xl border border-gray-100 bg-white/90 px-3 py-2 shadow-lg">
				<div class="flex flex-row flex-wrap sm:flex-nowrap justify-between items-center gap-2">
					<div class="text-center flex-1 min-w-[90px]">
						<p class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest">Total</p>
						<p class="text-lg font-black text-gray-900 mt-0.5">{{ $summary['total'] ?? 0 }}</p>
					</div>
					<div class="text-center flex-1 min-w-[90px]">
						<p class="text-[10px] font-bold text-blue-700 uppercase tracking-widest">Teknisi</p>
						<p class="text-lg font-black text-gray-900 mt-0.5">{{ $summary['teknisi'] ?? 0 }}</p>
					</div>
					<div class="text-center flex-1 min-w-[90px]">
						<p class="text-[10px] font-bold text-amber-700 uppercase tracking-widest">Admin</p>
						<p class="text-lg font-black text-gray-900 mt-0.5">{{ $summary['admin'] ?? 0 }}</p>
					</div>
				</div>
			</div>
		</div>
		<div class="flex justify-end mb-6">
			<form class="flex items-center gap-2" method="GET" action="">
				<input id="userSearchInput" type="text" name="search" placeholder="Search users..." class="w-44 bg-gray-100 border border-gray-200 rounded-lg focus:ring-emerald-200 focus:border-emerald-400 text-gray-700 placeholder-gray-400 text-sm outline-none h-10 px-4 transition" autocomplete="off" />
				<button type="submit" class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white transition p-0" title="Cari">
					<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" /></svg>
				</button>
				<a href="/user-management" class="ml-1 flex items-center justify-center h-8 w-8 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 transition p-0" title="Reset" style="text-decoration:none;">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
					</svg>
				</a>
				<button type="button" class="ml-2 flex items-center gap-2 px-4 h-10 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition shadow-sm" id="addUserBtn" onclick="openUserModal(false)">
					<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
					Tambah User
				</button>
			</form>
		</div>
		<div class="bg-white/80 rounded-xl border border-gray-100 p-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 shadow-sm">
			<div class="flex items-center gap-3">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-funnel w-5 h-5 text-emerald-400"><path d="M10 20a1 1 0 0 0 .553.895l2 1A1 1 0 0 0 14 21v-7a2 2 0 0 1 .517-1.341L21.74 4.67A1 1 0 0 0 21 3H3a1 1 0 0 0-.742 1.67l7.225 7.989A2 2 0 0 1 10 14z"></path></svg>
				<span class="text-sm font-medium text-gray-700">Filters:</span>
				<select class="border border-gray-200 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 bg-white transition" style="min-width:120px;">
					<option>All Roles</option>
					<option>Admin</option>
					<option>Teknisi</option>
				</select>
				<select class="border border-gray-200 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-yellow-200 focus:border-yellow-400 bg-white transition" style="min-width:120px;">
					<option>All Status</option>
					<option>Aktif</option>
					<option>Nonaktif</option>
				</select>
			</div>
			<div class="flex items-center gap-3">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-narrow-wide w-5 h-5 text-yellow-400"><path d="m3 8 4-4 4 4"></path><path d="M7 4v16"></path><path d="M11 12h4"></path><path d="M11 16h7"></path><path d="M11 20h10"></path></svg>
				<select class="border border-gray-200 rounded-md px-3 py-2 text-sm text-gray-700 focus:ring-2 focus:ring-yellow-200 focus:border-yellow-400 bg-white transition" style="min-width:140px;">
					<option>Name A–Z</option>
					<option>Name Z–A</option>
					<option>Terbaru</option>
					<option>Terlama</option>
				</select>
			</div>
		</div>
		<!-- Card Tabel User Modern -->
		<div class="relative overflow-x-auto bg-white rounded-2xl shadow-lg border border-gray-100 mb-8" style="border-radius: 24px !important;">
			<!-- Top Accent Bar -->
			<div class="absolute top-0 left-0 w-full h-1.5 bg-emerald-500/80 rounded-t-2xl"></div>
			<div class="p-6 md:p-8">
				<div class="flex items-center justify-between mb-6">
					<div class="flex items-center space-x-2.5">
						<div style="border-radius: 10px !important;" class="p-1.5 bg-emerald-50">
							<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
							</svg>
						</div>
						<h3 class="font-bold text-gray-900 text-lg">Tabel User</h3>
					</div>
				</div>
				<table class="min-w-full divide-y divide-gray-200">
					<thead class="bg-gray-50">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Profile</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Full Name</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email Address</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Phone Number</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Role</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Last Login</th>
							<th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Action</th>
						</tr>
					</thead>
					<tbody class="bg-white divide-y divide-gray-100">
						@forelse($users as $user)
							<tr class="hover:bg-emerald-50/40 transition">
								<!-- Profile (avatar) -->
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="flex items-center">
										<span class="inline-block h-10 w-10 rounded-full overflow-hidden bg-gray-200">
											@if(isset($user->profile_photo_url))
												<img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-10 w-10 object-cover">
											@else
												<svg class="h-10 w-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
													<path d="M24 24H0c0-6.627 5.373-12 12-12s12 5.373 12 12z"/>
													<circle cx="12" cy="8" r="4"/>
												</svg>
											@endif
										</span>
									</div>
								</td>
								<!-- Full Name -->
								<td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">{{ $user->name }}</td>
								<!-- Email Address -->
								<td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $user->email }}</td>
								<!-- Phone Number -->
								<td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $user->phone ?? '-' }}</td>
								<!-- Role -->
								<td class="px-6 py-4 whitespace-nowrap capitalize">
									@php
										$roleLabel = match ($user->role ?? null) {
											'operator' => 'Teknisi',
											'admin' => 'Admin',
											default => $user->role ?? '-',
										};
									@endphp
									<span class="inline-block px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700 font-semibold">{{ $roleLabel }}</span>
								</td>
								<!-- Status -->
								<td class="px-6 py-4 whitespace-nowrap">
									<span class="px-2 py-1 text-xs font-semibold rounded-full {{ ($user->status ?? true) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-500' }}">
										{{ ($user->status ?? true) ? 'Active' : 'Inactive' }}
									</span>
								</td>
								<!-- Last Login -->
								<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d M Y, H:i') : '-' }}</td>
								<!-- Action -->
								<td class="px-6 py-4 whitespace-nowrap text-center">
									<button onclick="editUser({{ $user->id }})" class="inline-flex items-center justify-center mr-2 p-2 rounded-full text-emerald-600 hover:bg-emerald-100 hover:text-emerald-800 transition" title="Edit">
										<!-- Pencil Icon -->
										<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213l-4 1 1-4 12.362-12.726z" />
										</svg>
									</button>
									<button onclick="confirmDeleteUser({{ $user->id }})" class="inline-flex items-center justify-center p-2 rounded-full text-red-600 hover:bg-red-100 hover:text-red-800 transition" title="Hapus">
										<!-- Trash Icon -->
										<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m2 0v12a2 2 0 01-2 2H8a2 2 0 01-2-2V7h12z" />
										</svg>
									</button>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="text-center py-8 text-gray-400">
									Tidak ada user ditemukan
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<!-- Modal Tambah/Edit User -->
		<div id="userModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/10 transition-all duration-200 backdrop-blur-sm supports-backdrop-blur hidden" style="backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);">
			<div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
				<h4 class="text-lg font-bold mb-4" id="userModalTitle">Tambah User</h4>
				<form id="userForm">
					<input type="hidden" id="userId" name="user_id">
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Nama</label>
						<input type="text" id="userName" name="name" class="w-full border rounded px-3 py-2" required>
					</div>
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Email</label>
						<input type="email" id="userEmail" name="email" class="w-full border rounded px-3 py-2" required>
					</div>
					<div class="mb-3" id="passwordField">
						<label class="block text-sm font-medium mb-1">Password</label>
						<input type="password" id="userPassword" name="password" class="w-full border rounded px-3 py-2">
					</div>
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Role</label>
						<select id="userRole" name="role" class="w-full border rounded px-3 py-2">
							<option value="admin">Admin</option>
							<option value="teknisi">Teknisi</option>
						</select>
					</div>
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Status</label>
						<select id="userStatus" name="status" class="w-full border rounded px-3 py-2">
							<option value="1">Aktif</option>
							<option value="0">Nonaktif</option>
						</select>
					</div>
					<div class="flex justify-end space-x-2 mt-4">
						<button type="button" onclick="closeUserModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
						<button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Simpan</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Modal Konfirmasi Hapus User -->
		<div id="deleteUserModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/10 transition-all duration-200 backdrop-blur-sm supports-backdrop-blur hidden" style="backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);">
			<div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 relative">
				<h4 class="text-lg font-bold mb-4">Konfirmasi Hapus User</h4>
				<p>Apakah Anda yakin ingin menghapus user ini?</p>
				<div class="flex justify-end space-x-2 mt-6">
					<button type="button" onclick="closeDeleteUserModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
					<button type="button" onclick="deleteUser()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
				</div>
			</div>
		</div>

		<!-- Modal Konfirmasi Reset Password -->
		<div id="resetPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/10 transition-all duration-200 backdrop-blur-sm supports-backdrop-blur hidden" style="backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);">
			<div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 relative">
				<h4 class="text-lg font-bold mb-4">Reset Password User</h4>
				<p>Reset password user ke default (misal: 12345678)?</p>
				<div class="flex justify-end space-x-2 mt-6">
					<button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
					<button type="button" onclick="resetPassword()" class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">Reset</button>
				</div>
			</div>
		</div>
	</div>
		@push('scripts')
			<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
			<script>
			// Live search user
			// document.addEventListener('DOMContentLoaded', function() {
			//     const searchInput = document.getElementById('userSearchInput');
			//     let searchTimeout = null;
			//     searchInput.addEventListener('input', function() {
			//         clearTimeout(searchTimeout);
			//         const query = this.value.trim();
			//         if (query.length === 0) {
			//             window.location.reload(); // Reset to all users if empty
			//             return;
			//         }
			//         if (query.length < 1) return; // Only search after 1+ chars
			//         searchTimeout = setTimeout(() => {
			//             fetch(`/user-management?search=${encodeURIComponent(query)}`)
			//                 .then(res => res.text())
			//                 .then(html => {
			//                     // Replace only the user table
			//                     const parser = new DOMParser();
			//                     const doc = parser.parseFromString(html, 'text/html');
			//                     const newTable = doc.querySelector('.relative.overflow-x-auto');
			//                     const oldTable = document.querySelector('.relative.overflow-x-auto');
			//                     if (newTable && oldTable) {
			//                         oldTable.innerHTML = newTable.innerHTML;
			//                     }
			//                 });
			//         }, 250);
			//     });
			// });

			// Modal logic
			function openUserModal(isEdit = false, user = null) {
			document.getElementById('userModal').classList.remove('hidden');
			document.getElementById('userForm').reset();
			document.getElementById('userId').value = '';
			document.getElementById('passwordField').classList.remove('hidden');
			document.getElementById('userModalTitle').textContent = 'Tambah User';
			if (isEdit && user) {
				document.getElementById('userModalTitle').textContent = 'Edit User';
				document.getElementById('userId').value = user.id;
				document.getElementById('userName').value = user.name;
				document.getElementById('userEmail').value = user.email;
				document.getElementById('userRole').value = user.role;
				document.getElementById('userStatus').value = user.status ? '1' : '0';
				document.getElementById('passwordField').classList.add('hidden');
			}
		}
		function closeUserModal() {
			document.getElementById('userModal').classList.add('hidden');
		}
		function confirmDeleteUser(id) {
			window.selectedUserId = id;
			document.getElementById('deleteUserModal').classList.remove('hidden');
		}
		function closeDeleteUserModal() {
			document.getElementById('deleteUserModal').classList.add('hidden');
		}
		function confirmResetPassword(id) {
			window.selectedUserId = id;
			document.getElementById('resetPasswordModal').classList.remove('hidden');
		}
		function closeResetPasswordModal() {
			document.getElementById('resetPasswordModal').classList.add('hidden');
		}

		// Toast notification
		function showToast(message, type = 'success') {
			const toast = document.createElement('div');
			toast.className = `mb-2 px-4 py-2 rounded shadow text-white font-semibold ${type === 'success' ? 'bg-emerald-600' : type === 'error' ? 'bg-red-600' : 'bg-yellow-600'}`;
			toast.textContent = message;
			document.getElementById('toast-container').appendChild(toast);
			setTimeout(() => toast.remove(), 3000);
		}

		document.addEventListener('DOMContentLoaded', function() {
			const addUserBtn = document.getElementById('addUserBtn');
			if (addUserBtn) {
				addUserBtn.addEventListener('click', function() {
					openUserModal(false);
				});
			}
		});

		// Submit form (add/edit)
		document.getElementById('userForm').onsubmit = async function(e) {
			e.preventDefault();
			const id = document.getElementById('userId').value;
			const url = id ? `/user-management/${id}` : '/user-management';
			const method = id ? 'PUT' : 'POST';
			const data = {
				name: document.getElementById('userName').value,
				email: document.getElementById('userEmail').value,
				role: document.getElementById('userRole').value,
				status: document.getElementById('userStatus').value,
			};
			if (!id) data.password = document.getElementById('userPassword').value;
			if (id && document.getElementById('userPassword').value) data.password = document.getElementById('userPassword').value;
			try {
				const response = await fetch(url, {
					method: method,
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
					},
					body: JSON.stringify(data)
				});
				const res = await response.json();
				if (res.success) {
					showToast(res.message, 'success');
					closeUserModal();
					setTimeout(() => location.reload(), 1000);
				} else {
					showToast(res.message || 'Gagal menyimpan user', 'error');
				}
			} catch (err) {
				showToast('Terjadi kesalahan', 'error');
			}
		};

		// Edit user (open modal with data)
		window.editUser = async function(id) {
			try {
				const response = await fetch(`/api/users/${id}`);
				const user = await response.json();
				openUserModal(true, user);
			} catch (err) {
				showToast('Gagal memuat data user', 'error');
			}
		};

		// Delete user
		window.deleteUser = async function() {
			const id = window.selectedUserId;
			try {
				const response = await fetch(`/user-management/${id}`, {
					method: 'DELETE',
					headers: {
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
					}
				});
				const res = await response.json();
				if (res.success) {
					showToast(res.message, 'success');
					closeDeleteUserModal();
					setTimeout(() => location.reload(), 1000);
				} else {
					showToast(res.message || 'Gagal menghapus user', 'error');
				}
			} catch (err) {
				showToast('Terjadi kesalahan', 'error');
			}
		};

		// Reset password
		window.resetPassword = async function() {
			const id = window.selectedUserId;
			try {
				const response = await fetch(`/user-management/${id}/reset-password`, {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
					}
				});
				const res = await response.json();
				if (res.success) {
					showToast(res.message, 'success');
					closeResetPasswordModal();
				} else {
					showToast(res.message || 'Gagal reset password', 'error');
				}
			} catch (err) {
				showToast('Terjadi kesalahan', 'error');
			}
		};
		</script>
	@endpush
</x-app-layout>
