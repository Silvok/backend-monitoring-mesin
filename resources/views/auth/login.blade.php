<x-guest-layout>
    <!-- Logo Centered -->
    <div class="flex justify-center mb-8">
        <x-monitor-logo size="lg" />
    </div>

    <!-- Welcome Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-emerald-900 mb-2">Halo, Selamat Datang</h1>
        <p class="text-gray-600">Masuk ke dashboard monitoring mesin Anda</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" autocomplete="on">
        @csrf

        <!-- Email Address -->
        <div class="mb-5">
            <label for="email" class="block text-sm font-semibold text-emerald-900 mb-2">
                Email
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="nama@example.com"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-600 focus:ring focus:ring-emerald-200 focus:outline-none transition duration-200"
                required
                autofocus
                autocomplete="email"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-5">
            <label for="password" class="block text-sm font-semibold text-emerald-900 mb-2">
                Password
            </label>
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="**********"
                    class="w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl focus:border-emerald-600 focus:ring focus:ring-emerald-200 focus:outline-none transition duration-200"
                    required
                    autocomplete="current-password"
                />
                <span
                    id="togglePassword"
                    class="absolute h-8 w-8 text-gray-600 hover:text-emerald-700 transition flex items-center justify-center cursor-pointer select-none"
                    style="right: 0.75rem; top: 50%; transform: translateY(-50%);"
                    role="button"
                    tabindex="0"
                    aria-label="Tampilkan password"
                >
                    <svg id="eyeOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <svg id="eyeClosed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.958 9.958 0 012.042-3.368M6.223 6.223A9.958 9.958 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.962 9.962 0 01-4.043 5.135M15 12a3 3 0 00-4.243-2.829M9.88 9.88A3 3 0 0012 15c.488 0 .953-.117 1.364-.325M3 3l18 18"></path>
                    </svg>
                </span>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-6">
            <label for="remember_me" class="flex items-center cursor-pointer">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="w-5 h-5 rounded border-gray-300 text-emerald-700 focus:ring-emerald-500 cursor-pointer"
                    name="remember"
                >
                <span class="ml-2 text-sm text-gray-700 font-medium">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-emerald-700 hover:text-emerald-900 font-semibold transition" href="{{ route('password.request') }}">
                    Lupa Password?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full bg-gradient-to-r from-emerald-700 to-emerald-800 hover:from-emerald-800 hover:to-emerald-900 text-white font-semibold py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200"
        >
            Masuk
        </button>

        <!-- Sign Up Link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Lupa kata sandi?
                <a href="mailto:admin@example.com" class="text-emerald-700 hover:text-emerald-900 font-semibold transition">
                    Hubungi Admin
                </a>
            </p>
        </div>
    </form>

    <!-- Script untuk Remember Me -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const rememberCheckbox = document.getElementById('remember_me');
            const togglePassword = document.getElementById('togglePassword');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            // Load saved credentials otomatis
            const savedEmail = localStorage.getItem('login_email');
            const savedPassword = localStorage.getItem('login_password');

            if (savedEmail) {
                emailInput.value = savedEmail;
            }
            if (savedPassword) {
                passwordInput.value = savedPassword;
                rememberCheckbox.checked = true;
            }

            // Handle form submission
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                // Jika checkbox "Ingat saya" dicentang, simpan credentials
                if (rememberCheckbox.checked) {
                    localStorage.setItem('login_email', emailInput.value);
                    localStorage.setItem('login_password', passwordInput.value);
                } else {
                    // Jika tidak dicentang, hapus credentials yang tersimpan
                    localStorage.removeItem('login_email');
                    localStorage.removeItem('login_password');
                }
            });

            // Ketika user uncheck "Ingat saya", clear credentials
            rememberCheckbox.addEventListener('change', function() {
                if (!this.checked) {
                    localStorage.removeItem('login_email');
                    localStorage.removeItem('login_password');
                }
            });

            function togglePasswordVisibility() {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                eyeOpen.classList.toggle('hidden', !isPassword);
                eyeClosed.classList.toggle('hidden', isPassword);
                togglePassword.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            }

            if (togglePassword) {
                togglePassword.addEventListener('click', togglePasswordVisibility);
                togglePassword.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        togglePasswordVisibility();
                    }
                });
            }

        });
    </script>
</x-guest-layout>
