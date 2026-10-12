<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

{{-- Kartu login --}}
<div class="bg-white w-full max-w-md rounded-xl border border-gray-300 shadow-sm p-8">

    {{-- Logo (ganti dengan <img> logo kamu nanti) --}}
    <div class="flex justify-center">
        <div class="w-20 h-20 border-2 border-gray-300 rounded-lg flex items-center justify-center text-gray-400 text-xs">
            LOGO
        </div>
    </div>

    {{-- Judul --}}
    <h1 class="text-3xl font-bold text-gray-800 text-center mt-4">Selamat Datang!</h1>
    <p class="text-gray-500 text-center">Masuk ke akun anda</p>

    {{-- Form --}}
    {{-- Ganti '#' dengan route proses login kamu nanti, misalnya route('login.proses') --}}
    <form method="POST"
          action="{{ route('login.proses') }}"
          class="mt-6">
        @csrf

        {{-- Input tersembunyi: menyimpan role yang dipilih (guru / admin) --}}
        <input type="hidden" name="role" id="role-input" value="{{ old('role', 'guru') }}">

        {{-- Pilihan role --}}
        <label class="block text-sm text-gray-600 mb-2">Masuk sebagai:</label>
        <div class="grid grid-cols-2 gap-2">
            <button type="button" id="btn-guru"
                    class="role-btn flex items-center justify-center gap-2 py-2 rounded-lg border text-sm font-medium transition">
                👤 Guru
            </button>
            <button type="button" id="btn-admin"
                    class="role-btn flex items-center justify-center gap-2 py-2 rounded-lg border text-sm font-medium transition">
                ⭐ Admin
            </button>
        </div>

        {{-- Pesan error (kalau login gagal) --}}
        @if ($errors->any())
            <div class="mt-4 p-3 rounded-lg bg-red-50 text-red-600 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Username --}}
        <div class="mt-4">
            <label for="username" class="block text-sm text-gray-600 mb-1">Username</label>
            <input type="text" id="username" name="username"
                   value="{{ old('username') }}"
                   placeholder="username"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <label for="password" class="block text-sm text-gray-600 mb-1">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="password"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- Tombol Masuk --}}
        <button type="submit"
                class="w-full mt-6 py-2 rounded-lg bg-blue-500 hover:bg-blue-600
                       text-white font-semibold transition">
            Masuk
        </button>

        {{-- Link daftar: HANYA tampil untuk Guru (sesuai wireframe) --}}
        <p id="register-text" class="text-center text-xs text-gray-500 mt-3">
            Belum punya akun?
            <a href="{{ Route::has('register') ? route('register') : '#' }}"
               class="text-blue-500 hover:underline">Daftar di sini</a>
        </p>
            {{-- link untuk forgot password --}}
        <p id="forgot-password" class="text-center text-xs text-gray-500 mt-3">
            <a href="{{ Route::has('#') ? route('#') : '#' }}"
               class="text-blue-500 hover:underline">lupa password?</a>
        </p>
    </form>
</div>
<script src="{{ asset('js/login.js') }}"></script>

</body>
</html>