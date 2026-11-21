<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-body">

{{-- Kartu login --}}
<div class="auth-card">

    {{-- Logo --}}
    <div class="auth-logo">
        <img src="{{ asset('images/logo.webp') }}" alt="Logo Ngiring Malajah Basa Bali">
    </div>

    {{-- Judul --}}
    <h1 class="auth-title">Selamat Datang!</h1>
    <p class="auth-subtitle">Masuk ke akun anda</p>

    {{-- Form --}}
    <form method="POST" action="{{ route('login.proses') }}" class="auth-form">
        @csrf

        {{-- Input tersembunyi: menyimpan role yang dipilih (guru / admin) --}}
        <input type="hidden" name="role" id="role-input" value="{{ old('role', 'guru') }}">

        {{-- Pilihan role --}}
        <span class="auth-label">Masuk sebagai:</span>
        <div class="role-group">
            <button type="button" id="btn-guru" class="role-btn">👤 Guru</button>
            <button type="button" id="btn-admin" class="role-btn">⭐ Admin</button>
        </div>

        {{-- Pesan error (kalau login gagal) --}}
        @if ($errors->any())
            <div class="auth-alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Username --}}
        <div class="auth-field">
            <label for="username" class="auth-label">Username</label>
            <input type="text" id="username" name="username"
                   value="{{ old('username') }}"
                   placeholder="username"
                   required
                   class="auth-input">
        </div>

        {{-- Password --}}
        <div class="auth-field">
            <label for="password" class="auth-label">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="password"
                   required
                   class="auth-input">
        </div>

        {{-- Tombol Masuk --}}
        <button type="submit" class="auth-btn">Masuk</button>

        {{-- Link daftar: HANYA tampil untuk Guru --}}
        <p id="register-text" class="auth-text">
            Belum punya akun?
            <a href="{{ Route::has('register') ? route('register') : '#' }}" class="auth-link">Daftar di sini</a>
        </p>

        {{-- Tombol kembali --}}
        <a href="{{ route('game.index') }}" class="auth-back">← Kembali ke beranda</a>
    </form>
</div>

<script src="{{ asset('js/login.js') }}"></script>

</body>
</html>