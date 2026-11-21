// Ambil elemen-elemen yang dibutuhkan
const roleInput    = document.getElementById('role-input');
const btnGuru      = document.getElementById('btn-guru');
const btnAdmin     = document.getElementById('btn-admin');
const registerText = document.getElementById('register-text');

// Ganti role yang dipilih
function pilihRole(role) {
    // 1. Simpan role ke input tersembunyi (ikut terkirim saat form disubmit)
    roleInput.value = role;

    // 2. Tandai tombol yang aktif (gaya ada di auth.css -> .role-btn.is-active)
    btnGuru.classList.toggle('is-active', role === 'guru');
    btnAdmin.classList.toggle('is-active', role === 'admin');
    btnGuru.setAttribute('aria-pressed', role === 'guru');
    btnAdmin.setAttribute('aria-pressed', role === 'admin');

    // 3. Tampilkan link "Daftar" hanya untuk guru
    registerText.hidden = role !== 'guru';
}

// Saat tombol diklik
btnGuru.addEventListener('click',  () => pilihRole('guru'));
btnAdmin.addEventListener('click', () => pilihRole('admin'));

// Saat halaman pertama dibuka, pakai role yang tersimpan (default: guru)
pilihRole(roleInput.value);