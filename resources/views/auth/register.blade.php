<x-guest-layout>
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-emerald-900 mb-2">Daftar Akun Baru</h1>
        <p class="text-gray-600">Bergabung dengan sistem monitoring mesin</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-5">
            <label for="name" class="block text-sm font-semibold text-emerald-900 mb-2">
                Nama Lengkap
            </label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="John Doe"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-600 focus:ring focus:ring-emerald-200 focus:outline-none transition duration-200"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

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
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-semibold text-emerald-900 mb-2">
                Konfirmasi Password
            </label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                placeholder="••••••••••"
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-emerald-600 focus:ring focus:ring-emerald-200 focus:outline-none transition duration-200"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full bg-gradient-to-r from-emerald-700 to-emerald-800 hover:from-emerald-800 hover:to-emerald-900 text-white font-semibold py-3.5 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200"
        >
            Daftar
        </button>

        <!-- Login Link -->
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-emerald-700 hover:text-emerald-900 font-semibold transition">
                    Masuk Sekarang
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
