<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
          action="{{ Route::has('login.proses') ? route('login.proses') : '#' }}"
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
                   placeholder="Input username"
                   required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <label for="password" class="block text-sm text-gray-600 mb-1">Password</label>
            <input type="password" id="password" name="password"
                   placeholder="Input password"
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
    </form>
</div>

<script>
    // Ambil elemen-elemen yang dibutuhkan
    const roleInput    = document.getElementById('role-input');
    const btnGuru      = document.getElementById('btn-guru');
    const btnAdmin     = document.getElementById('btn-admin');
    const registerText = document.getElementById('register-text');

    // Class untuk tombol yang AKTIF dan TIDAK AKTIF
    const aktif      = ['bg-gray-300', 'border-gray-400', 'text-gray-800'];
    const tidakAktif = ['bg-white', 'border-gray-300', 'text-gray-500'];

    // Fungsi untuk mengganti role yang dipilih
    function pilihRole(role) {
        // 1. Simpan role ke input tersembunyi (ikut terkirim saat form disubmit)
        roleInput.value = role;

        // 2. Ubah warna tombol
        [btnGuru, btnAdmin].forEach(btn => {
            btn.classList.remove(...aktif, ...tidakAktif);
            btn.classList.add(...tidakAktif);
        });
        const tombolDipilih = role === 'guru' ? btnGuru : btnAdmin;
        tombolDipilih.classList.remove(...tidakAktif);
        tombolDipilih.classList.add(...aktif);

        // 3. Tampilkan link "Daftar" hanya untuk guru
        registerText.classList.toggle('hidden', role !== 'guru');
    }

    // Saat tombol diklik
    btnGuru.addEventListener('click',  () => pilihRole('guru'));
    btnAdmin.addEventListener('click', () => pilihRole('admin'));

    // Saat halaman pertama dibuka, pakai role yang tersimpan (default: guru)
    pilihRole(roleInput.value);
</script>

</body>
</html>
