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