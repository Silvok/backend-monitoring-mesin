<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Lupa password? Silakan masukkan email Anda untuk menerima link reset password.
    </div>

    <div class="mb-4 text-sm text-gray-600">
        Jika tidak menerima email atau masih kesulitan login,
        <a
            href="https://wa.me/6287825729377?text=Selamat%20siang%20Admin.%0A%0APerkenalkan%2C%20saya%20pengguna%20aplikasi%20PreMaint%20dengan%20detail%20berikut%3A%0A-%20Nama%3A%20%0A-%20Email%20Akun%3A%20%0A-%20Waktu%20Permintaan%3A%20%0A%0ASaya%20mengalami%20kendala%20lupa%20kata%20sandi%20dan%20memohon%20bantuan%20untuk%20reset%20password.%0ATerima%20kasih%20atas%20bantuannya."
            target="_blank"
            rel="noopener noreferrer"
            class="text-emerald-700 hover:text-emerald-900 font-semibold transition"
        >
            Hubungi Admin (WhatsApp)
        </a>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
