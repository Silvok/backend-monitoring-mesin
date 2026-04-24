

<x-app-layout>
	@section('title', 'Manajemen User')
	@push('styles')
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
		<style>
			.user-table-desktop { display: block; }
			.user-table-mobile { display: none; }
			.user-add-btn {
				width: 100%;
			}
			.user-header-row {
				display: flex;
				flex-wrap: nowrap;
				align-items: center;
				justify-content: space-between;
				gap: 0.75rem;
				width: 100%;
				min-width: 0;
			}
			.user-header-title {
				display: flex;
				align-items: center;
				flex: 1 1 auto;
				min-width: 0;
			}
			.user-time-wrap {
				display: flex;
				width: auto;
				margin-left: auto;
				flex: 0 0 auto;
			}
			.user-time-chip {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				gap: 0.5rem;
				width: auto;
				max-width: 100%;
				padding: 0.375rem 0.75rem;
				border: 1px solid rgb(229 231 235);
				border-radius: 0.5rem;
				background: rgb(249 250 251);
				color: rgb(75 85 99);
				font-size: 0.75rem;
				line-height: 1rem;
			}

			@media (max-width: 1023.98px) {
				.user-table-desktop { display: none !important; }
				.user-table-mobile { display: block; }
			}

			@media (max-width: 639.98px) {
				.user-time-chip {
					padding: 0.25rem 0.5rem;
					font-size: 0.6875rem;
					line-height: 1rem;
				}
			}

			@media (max-width: 389.98px) {
				.user-header-row {
					flex-wrap: wrap;
					align-items: flex-start;
				}
				.user-header-title {
					flex: 1 1 100%;
				}
				.user-time-wrap {
					margin-left: 0;
				}
			}

			@media (min-width: 1024px) {
				.user-header-row {
					gap: 1rem;
				}
				.user-add-btn {
					width: auto;
					min-width: 190px;
				}
				.user-header-title {
					flex: 1 1 auto;
				}
				.user-time-wrap {
					width: auto;
					justify-content: flex-end;
				}
				.user-time-chip {
					width: auto;
					min-width: 210px;
					font-size: 0.875rem;
					line-height: 1.25rem;
				}
			}
		</style>
	@endpush

	<x-slot name="header">
		<div class="user-header-row">
			<div class="user-header-title">
				<h2 class="font-bold text-xl text-emerald-900 leading-tight min-w-0 whitespace-nowrap">
					{{ __('messages.users.title') }}
				</h2>
			</div>
			<div class="user-time-wrap">
				<div class="user-time-chip">
					<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
						<circle cx="12" cy="12" r="9"></circle>
						<path d="M12 7v5l3 2"></path>
					</svg>
					<span class="font-semibold whitespace-nowrap" id="currentTime">{{ now()->format('d M Y, H:i:s') }}</span>
				</div>
			</div>
		</div>
	</x-slot>

	<div class="py-6 px-4 sm:px-6 lg:px-8">
		<!-- Notifikasi -->
		<div id="toast-container"></div>

		<div class="mb-4">
			<div class="rounded-xl border border-gray-100 bg-white/90 px-3 py-2 shadow-lg">
				<div class="flex items-center justify-between gap-2">
					<div class="flex-1 text-center min-w-0">
						<p class="text-[10px] font-bold text-emerald-700 uppercase tracking-widest whitespace-nowrap">Total</p>
						<p class="text-lg font-black text-gray-900 mt-0.5">{{ $summary['total'] ?? 0 }}</p>
					</div>
					<div class="flex-1 text-center min-w-0">
						<p class="text-[10px] font-bold text-amber-700 uppercase tracking-widest whitespace-nowrap">Super Admin</p>
						<p class="text-lg font-black text-gray-900 mt-0.5">{{ $summary['super_admin'] ?? 0 }}</p>
					</div>
					<div class="flex-1 text-center min-w-0">
						<p class="text-[10px] font-bold text-blue-700 uppercase tracking-widest whitespace-nowrap">Admin</p>
						<p class="text-lg font-black text-gray-900 mt-0.5">{{ $summary['admin'] ?? 0 }}</p>
					</div>
					<div class="flex-1 text-center min-w-0">
						<p class="text-[10px] font-bold text-purple-700 uppercase tracking-widest whitespace-nowrap">Koordinator</p>
						<p class="text-lg font-black text-gray-900 mt-0.5">{{ $summary['koordinator'] ?? 0 }}</p>
					</div>
				</div>
			</div>
		</div>
		<div class="mb-6">
			<form class="w-full flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3" method="GET" action="">
				<div class="w-full lg:flex-1 lg:max-w-xl flex items-center gap-2 min-w-0">
					<input id="userSearchInput" type="text" name="search" placeholder="{{ __('messages.users.search_placeholder') }}" class="flex-1 min-w-0 bg-gray-100 border border-gray-200 rounded-lg focus:ring-emerald-200 focus:border-emerald-400 text-gray-700 placeholder-gray-400 text-sm outline-none h-10 px-4 transition" autocomplete="off" />
					<button type="submit" class="flex items-center justify-center h-10 w-10 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white transition p-0 shrink-0" title="Cari">
						<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" /></svg>
					</button>
					<a href="/user-management" class="flex items-center justify-center h-10 w-10 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 transition p-0 shrink-0" title="Reset" style="text-decoration:none;">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
						</svg>
					</a>
				</div>
				<button type="button" class="user-add-btn inline-flex items-center justify-center gap-2 px-5 h-10 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium transition shadow-sm whitespace-nowrap shrink-0" id="addUserBtn" onclick="openUserModal(false)">
					<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
					{{ __('messages.users.add') }}
				</button>
			</form>
		</div>
		<!-- Card Tabel User Modern -->
		<div class="user-table-desktop relative overflow-x-auto bg-white rounded-2xl shadow-lg border border-gray-100 mb-8" style="border-radius: 24px !important;">
			<!-- Top Accent Bar -->
			<div class="absolute top-0 left-0 w-full h-1.5 bg-emerald-500/80 rounded-t-2xl"></div>
			<div class="p-4 sm:p-6 md:p-8">
				<div class="flex items-center justify-between mb-6">
					<div class="flex items-center space-x-2.5">
						<div style="border-radius: 10px !important;" class="p-1.5 bg-emerald-50">
							<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
							</svg>
						</div>
						<h3 class="font-bold text-gray-900 text-lg">{{ __('messages.users.table_title') }}</h3>
					</div>
				</div>
				<table class="min-w-full divide-y divide-gray-200">
					<thead class="bg-gray-50">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">{{ __('messages.users.profile') }}</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">{{ __('messages.users.full_name') }}</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">{{ __('messages.users.email') }}</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">{{ __('messages.users.phone') }}</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">WA Notif</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">{{ __('messages.users.role') }}</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">{{ __('messages.users.status') }}</th>
							<th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">{{ __('messages.users.last_login') }}</th>
							<th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">{{ __('messages.users.action') }}</th>
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
								<!-- WA Notification -->
								<td class="px-6 py-4 whitespace-nowrap">
									<span class="px-2 py-1 text-xs font-semibold rounded-full {{ ($user->wa_notification_enabled ?? true) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-500' }}">
										{{ ($user->wa_notification_enabled ?? true) ? 'On' : 'Off' }}
									</span>
								</td>
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
									<button onclick="confirmResetPassword({{ $user->id }}, @js($user->name))" class="inline-flex items-center justify-center mr-2 p-2 rounded-full text-amber-600 hover:bg-amber-100 hover:text-amber-800 transition" title="Ubah Password">
										<!-- Key Icon -->
										<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
											<circle cx="8" cy="10" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></circle>
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 10h10m-3 0v3m-3-3v3"></path>
										</svg>
									</button>
									<button onclick="confirmForceResetPassword({{ $user->id }}, @js($user->name))" class="inline-flex items-center justify-center mr-2 p-2 rounded-full text-blue-600 hover:bg-blue-100 hover:text-blue-800 transition" title="Reset Password (Lupa Password)">
										<!-- Reset Icon -->
										<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9M20 20v-5h-.581m0 0a8.003 8.003 0 01-15.357-2" />
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
								<td colspan="9" class="text-center py-8 text-gray-400">
									Tidak ada user ditemukan
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<div class="user-table-mobile bg-white rounded-2xl shadow-lg border border-gray-100 mb-8 overflow-hidden">
			<div class="h-1.5 bg-emerald-500/80"></div>
			<div class="p-4">
				<div class="flex items-center space-x-2.5 mb-4">
					<div class="p-1.5 bg-emerald-50 rounded-lg">
						<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
						</svg>
					</div>
					<h3 class="font-bold text-gray-900 text-lg">{{ __('messages.users.table_title') }}</h3>
				</div>

				<div class="space-y-3">
					@forelse($users as $user)
						<div class="rounded-xl border border-gray-100 bg-gray-50/70 p-3">
							<div class="flex items-start justify-between gap-3">
								<div class="flex items-center gap-3 min-w-0">
									<span class="inline-block h-10 w-10 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
										@if(isset($user->profile_photo_url))
											<img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-10 w-10 object-cover">
										@else
											<svg class="h-10 w-10 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
												<path d="M24 24H0c0-6.627 5.373-12 12-12s12 5.373 12 12z"/>
												<circle cx="12" cy="8" r="4"/>
											</svg>
										@endif
									</span>
									<div class="min-w-0">
										<p class="font-semibold text-gray-900 truncate">{{ $user->name }}</p>
										<p class="text-sm text-gray-600 truncate">{{ $user->email }}</p>
									</div>
								</div>
								<div class="flex items-center gap-1 flex-shrink-0">
									<button onclick="editUser({{ $user->id }})" class="inline-flex items-center justify-center p-2 rounded-full text-emerald-600 hover:bg-emerald-100 hover:text-emerald-800 transition" title="Edit">
										<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213l-4 1 1-4 12.362-12.726z" />
										</svg>
									</button>
									<button onclick="confirmResetPassword({{ $user->id }}, @js($user->name))" class="inline-flex items-center justify-center p-2 rounded-full text-amber-600 hover:bg-amber-100 hover:text-amber-800 transition" title="Ubah Password">
										<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
											<circle cx="8" cy="10" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></circle>
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 10h10m-3 0v3m-3-3v3"></path>
										</svg>
									</button>
									<button onclick="confirmForceResetPassword({{ $user->id }}, @js($user->name))" class="inline-flex items-center justify-center p-2 rounded-full text-blue-600 hover:bg-blue-100 hover:text-blue-800 transition" title="Reset Password (Lupa Password)">
										<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9M20 20v-5h-.581m0 0a8.003 8.003 0 01-15.357-2" />
										</svg>
									</button>
									<button onclick="confirmDeleteUser({{ $user->id }})" class="inline-flex items-center justify-center p-2 rounded-full text-red-600 hover:bg-red-100 hover:text-red-800 transition" title="Hapus">
										<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m2 0v12a2 2 0 01-2 2H8a2 2 0 01-2-2V7h12z" />
										</svg>
									</button>
								</div>
							</div>

							<div class="mt-3 grid grid-cols-2 gap-2 text-xs">
								@php
									$roleLabel = match ($user->role ?? null) {
										'operator' => 'Teknisi',
										'admin' => 'Admin',
										default => $user->role ?? '-',
									};
								@endphp
								<div>
									<p class="text-gray-500 mb-1">Role</p>
									<span class="inline-block px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700 font-semibold">{{ $roleLabel }}</span>
								</div>
								<div>
									<p class="text-gray-500 mb-1">Status</p>
									<span class="px-2 py-1 text-xs font-semibold rounded-full {{ ($user->status ?? true) ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-500' }}">
										{{ ($user->status ?? true) ? 'Active' : 'Inactive' }}
									</span>
								</div>
							</div>
							<p class="mt-2 text-xs text-gray-500">Last login: {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d M Y, H:i') : '-' }}</p>
						</div>
					@empty
						<div class="text-center py-8 text-gray-400">
							Tidak ada user ditemukan
						</div>
					@endforelse
				</div>
			</div>
		</div>

		<!-- Modal Tambah/Edit User -->
		<div id="userModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/10 transition-all duration-200 backdrop-blur-sm supports-backdrop-blur hidden" style="backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);">
			<div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
				<h4 class="text-lg font-bold mb-4" id="userModalTitle">{{ __('messages.users.add') }}</h4>
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
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Nomor WA</label>
						<input type="text" id="userPhone" name="phone" class="w-full border rounded px-3 py-2" placeholder="628xxxxxxxxxx" required>
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
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Notifikasi WA</label>
						<select id="userWaNotification" name="wa_notification_enabled" class="w-full border rounded px-3 py-2">
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

		<!-- Modal Ubah Password -->
		<div id="resetPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/10 transition-all duration-200 backdrop-blur-sm supports-backdrop-blur hidden" style="backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);">
			<div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
				<h4 class="text-lg font-bold mb-4">Ubah Password</h4>
				<div class="mb-4 rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm text-blue-800">
					Mengubah password untuk: <span id="resetPasswordTargetName" class="font-semibold">-</span>
				</div>
				<form id="resetPasswordForm">
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Password Saat Ini (Admin)</label>
						<input type="password" id="currentPassword" class="w-full border rounded px-3 py-2" placeholder="Masukkan password admin saat ini" required>
					</div>
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Password Baru</label>
						<input type="password" id="newPassword" class="w-full border rounded px-3 py-2" placeholder="Minimal 8 karakter" minlength="8" required>
					</div>
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
						<input type="password" id="newPasswordConfirmation" class="w-full border rounded px-3 py-2" placeholder="Ketik ulang password baru" minlength="8" required>
					</div>
					<div class="flex justify-end space-x-2 mt-6">
						<button type="button" onclick="closeResetPasswordModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
						<button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Ubah Password</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Modal Reset Password (Lupa Password) -->
		<div id="forceResetPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/10 transition-all duration-200 backdrop-blur-sm supports-backdrop-blur hidden" style="backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);">
			<div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
				<h4 class="text-lg font-bold mb-4">Reset Password</h4>
				<div class="mb-4 rounded-md border border-yellow-200 bg-yellow-50 px-3 py-2 text-sm text-yellow-800">
					Reset password untuk: <span id="forceResetPasswordTargetName" class="font-semibold">-</span><br>
					Password lama tidak diperlukan karena user lupa password.
				</div>
				<form id="forceResetPasswordForm">
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Password Baru</label>
						<input type="password" id="forceNewPassword" class="w-full border rounded px-3 py-2" placeholder="Minimal 8 karakter" minlength="8" required>
					</div>
					<div class="mb-3">
						<label class="block text-sm font-medium mb-1">Konfirmasi Password Baru</label>
						<input type="password" id="forceNewPasswordConfirmation" class="w-full border rounded px-3 py-2" placeholder="Ketik ulang password baru" minlength="8" required>
					</div>
					<div class="flex justify-end space-x-2 mt-6">
						<button type="button" onclick="closeForceResetPasswordModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Batal</button>
						<button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Reset Password</button>
					</div>
				</form>
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
			const userModalLabels = {
				add: @json(__('messages.users.add')),
				edit: @json(__('messages.users.title')),
			};

			function openUserModal(isEdit = false, user = null) {
			document.getElementById('userModal').classList.remove('hidden');
			document.getElementById('userForm').reset();
			document.getElementById('userId').value = '';
			document.getElementById('passwordField').classList.remove('hidden');
			document.getElementById('userModalTitle').textContent = userModalLabels.add;
			if (isEdit && user) {
				document.getElementById('userModalTitle').textContent = userModalLabels.edit;
				document.getElementById('userId').value = user.id;
				document.getElementById('userName').value = user.name;
				document.getElementById('userEmail').value = user.email;
				document.getElementById('userPhone').value = user.phone || '';
				document.getElementById('userRole').value = user.role;
				document.getElementById('userStatus').value = user.status ? '1' : '0';
				document.getElementById('userWaNotification').value = user.wa_notification_enabled ? '1' : '0';
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
		function confirmResetPassword(id, name) {
			window.selectedUserId = id;
			document.getElementById('resetPasswordTargetName').textContent = name || '-';
			document.getElementById('resetPasswordModal').classList.remove('hidden');
		}
		function closeResetPasswordModal() {
			document.getElementById('resetPasswordModal').classList.add('hidden');
			document.getElementById('resetPasswordForm').reset();
		}
		function confirmForceResetPassword(id, name) {
			window.selectedUserId = id;
			document.getElementById('forceResetPasswordTargetName').textContent = name || '-';
			document.getElementById('forceResetPasswordModal').classList.remove('hidden');
		}
		function closeForceResetPasswordModal() {
			document.getElementById('forceResetPasswordModal').classList.add('hidden');
			document.getElementById('forceResetPasswordForm').reset();
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
				phone: document.getElementById('userPhone').value,
				role: document.getElementById('userRole').value,
				status: document.getElementById('userStatus').value,
				wa_notification_enabled: document.getElementById('userWaNotification').value,
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

		// Reset password by admin (with admin current password verification)
		window.resetPassword = async function() {
			const id = window.selectedUserId;
			const currentPassword = document.getElementById('currentPassword').value;
			const newPassword = document.getElementById('newPassword').value;
			const newPasswordConfirmation = document.getElementById('newPasswordConfirmation').value;

			if (newPassword !== newPasswordConfirmation) {
				showToast('Konfirmasi password baru tidak sama', 'error');
				return;
			}

			try {
				const response = await fetch(`/user-management/${id}/reset-password`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
					},
					body: JSON.stringify({
						current_password: currentPassword,
						new_password: newPassword,
						new_password_confirmation: newPasswordConfirmation
					})
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

		window.forceResetPassword = async function() {
			const id = window.selectedUserId;
			const newPassword = document.getElementById('forceNewPassword').value;
			const newPasswordConfirmation = document.getElementById('forceNewPasswordConfirmation').value;

			if (newPassword !== newPasswordConfirmation) {
				showToast('Konfirmasi password baru tidak sama', 'error');
				return;
			}

			try {
				const response = await fetch(`/user-management/${id}/force-reset-password`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
					},
					body: JSON.stringify({
						new_password: newPassword,
						new_password_confirmation: newPasswordConfirmation
					})
				});
				const res = await response.json();
				if (res.success) {
					showToast(res.message, 'success');
					closeForceResetPasswordModal();
				} else {
					showToast(res.message || 'Gagal reset password', 'error');
				}
			} catch (err) {
				showToast('Terjadi kesalahan', 'error');
			}
		};

		document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
			e.preventDefault();
			window.resetPassword();
		});
		document.getElementById('forceResetPasswordForm').addEventListener('submit', function(e) {
			e.preventDefault();
			window.forceResetPassword();
		});
		</script>
	@endpush
</x-app-layout>
