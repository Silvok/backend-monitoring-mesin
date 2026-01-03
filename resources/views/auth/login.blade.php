<x-guest-layout>
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-emerald-900 mb-2">Halo, Selamat Datang Kembali!</h1>
        <p class="text-gray-600">Masuk ke dashboard monitoring mesin Anda</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
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
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-5">
            <label for="password" class="block text-sm font-semibold text-emerald-900 mb-2">
                Password
            </label>
            <input
                id="password"
                type="password"
                name="password"
                placeholder="••••••••••"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-600 focus:ring focus:ring-emerald-200 focus:outline-none transition duration-200"
                required
                autocomplete="current-password"
            />
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
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-emerald-700 hover:text-emerald-900 font-semibold transition">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
