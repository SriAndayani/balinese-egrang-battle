<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Guru</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

{{-- Kartu registrasi --}}
<div class="bg-white w-full max-w-md rounded-xl border border-gray-300 shadow-sm p-8">

    {{-- Logo (ganti dengan <img> logo kamu nanti) --}}
    <div class="flex justify-center">
        <div class="w-20 h-20 border-2 border-gray-300 rounded-lg flex items-center justify-center text-gray-400 text-xs">
            LOGO
        </div>
    </div>

    {{-- Judul --}}
    <h1 class="text-3xl font-bold text-gray-800 text-center mt-4">Daftar Akun Guru</h1>
    <p class="text-gray-500 text-center">Buat akun baru</p>

    <form method="POST" action="{{ route('register.proses') }}" class="mt-6">
        @csrf

        {{-- Username --}}
        <div>
            <label for="username" class="block text-sm text-gray-600 mb-1">Username</label>
            <input type="text" id="username" name="username"
                   value="{{ old('username') }}"
                   placeholder="Input username"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-400
                          @error('username') border-red-400 @enderror">
            @error('username')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mt-4">
            <label for="email" class="block text-sm text-gray-600 mb-1">Email</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}"
                   placeholder="Input email"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-400
                          @error('email') border-red-400 @enderror">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <label for="password" class="block text-sm text-gray-600 mb-1">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="Minimal 6 karakter"
                   required
                   autocomplete="new-password"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-400
                          @error('password') border-red-400 @enderror">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ulangi Password --}}
        <div class="mt-4">
            <label for="password_confirmation" class="block text-sm text-gray-600 mb-1">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   placeholder="Ketik ulang password"
                   required
                   autocomplete="new-password"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- Tombol Daftar --}}
        <button type="submit"
                class="w-full mt-6 py-2 rounded-lg bg-blue-500 hover:bg-blue-600
                       text-white font-semibold transition">
            Daftar
        </button>

        {{-- Kembali ke login --}}
        <p class="text-center text-xs text-gray-500 mt-3">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Masuk di sini</a>
        </p>

        <p class="text-center text-xs text-red-500 mt-3">
            *akun guru memerlukan verifikasi dari admin
        </p>
    </form>
</div>

</body>
</html>