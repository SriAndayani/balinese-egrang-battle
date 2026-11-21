<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Guru</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-body">

{{-- Kartu registrasi --}}
<div class="auth-card">

    {{-- Logo --}}
    <div class="auth-logo">
        <img src="{{ asset('images/logo.webp') }}" alt="Logo Ngiring Malajah Basa Bali">
    </div>

    {{-- Judul --}}
    <h1 class="auth-title">Daftar Akun Guru</h1>
    <p class="auth-subtitle">Buat akun baru</p>

    <form method="POST" action="{{ route('register.proses') }}" class="auth-form">
        @csrf

        {{-- Username --}}
        <div class="auth-field">
            <label for="username" class="auth-label">Username</label>
            <input type="text" id="username" name="username"
                   value="{{ old('username') }}"
                   placeholder="Input username"
                   required
                   class="auth-input @error('username') is-invalid @enderror">
            @error('username')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="auth-field">
            <label for="email" class="auth-label">Email</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}"
                   placeholder="Input email"
                   required
                   class="auth-input @error('email') is-invalid @enderror">
            @error('email')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="auth-field">
            <label for="password" class="auth-label">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="Minimal 6 karakter"
                   required
                   autocomplete="new-password"
                   class="auth-input @error('password') is-invalid @enderror">
            @error('password')
                <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="auth-field">
            <label for="password_confirmation" class="auth-label">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   placeholder="Ketik ulang password"
                   required
                   autocomplete="new-password"
                   class="auth-input">
        </div>

        {{-- Tombol Daftar --}}
        <button type="submit" class="auth-btn">Daftar</button>

        {{-- Kembali ke login --}}
        <p class="auth-text">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="auth-link">Masuk di sini</a>
        </p>

        <p class="auth-note">*akun guru memerlukan verifikasi dari admin</p>

        {{-- Tombol kembali --}}
        <a href="{{ route('game.index') }}" class="auth-back">← Kembali ke beranda</a>
    </form>
</div>

</body>
</html>