// public/js/rooms.js
// Tugas file ini ada dua:
//   1. Membagi daftar room menjadi beberapa halaman, lalu menggerakkan tombol panah.
//   2. Memperbarui daftar room tiap 5 detik tanpa memuat ulang halaman.

// ---------- 1. Ambil elemen yang dibutuhkan dari halaman ----------
var daftar      = document.getElementById("room-list"); // wadah semua kartu room
var tombolKiri  = document.getElementById("prev");      // panah kiri
var tombolKanan = document.getElementById("next");      // panah kanan

// Halaman yang sedang dilihat. 0 = halaman pertama.
var halamanSekarang = 0;

// ---------- 2. Berapa room yang tampil per halaman? ----------
// Angkanya TIDAK ditulis di sini, tapi dibaca dari CSS (variabel --per-halaman di rooms.css).
// Jadi mengubah jumlah per halaman (atau per orientasi layar) cukup di SATU tempat: CSS.
function jumlahPerHalaman() {
  var nilai = parseInt(
    getComputedStyle(document.body).getPropertyValue("--per-halaman"),
    10
  );
  if (nilai > 0) {
    return nilai;
  }
  return 3;   // cadangan kalau variabelnya tidak terbaca
}

// ---------- 3. Tampilkan room sesuai halaman ----------
function tampilkanHalaman() {
  var kartu = daftar.children;            // semua kartu room
  var perHalaman = jumlahPerHalaman();
  var jumlahHalaman = Math.ceil(kartu.length / perHalaman);

  if (jumlahHalaman < 1) {
    jumlahHalaman = 1;
  }

  // Jaga supaya halaman tidak melebihi jumlah halaman (misalnya saat room berkurang)
  if (halamanSekarang > jumlahHalaman - 1) {
    halamanSekarang = jumlahHalaman - 1;
  }

  // Kartu yang masuk halaman sekarang ditampilkan, sisanya disembunyikan
  for (var i = 0; i < kartu.length; i++) {
    var nomorHalamanKartu = Math.floor(i / perHalaman);
    kartu[i].hidden = (nomorHalamanKartu !== halamanSekarang);
  }

  // Panah hanya terlihat kalau halamannya lebih dari satu
  var adaBanyakHalaman = jumlahHalaman > 1;
  tombolKiri.style.visibility  = adaBanyakHalaman ? "visible" : "hidden";
  tombolKanan.style.visibility = adaBanyakHalaman ? "visible" : "hidden";

  // Panah kiri mati di halaman pertama, panah kanan mati di halaman terakhir
  tombolKiri.disabled  = (halamanSekarang === 0);
  tombolKanan.disabled = (halamanSekarang === jumlahHalaman - 1);
}

// ---------- 4. Klik panah ----------
tombolKiri.addEventListener("click", function () {
  halamanSekarang = halamanSekarang - 1;
  tampilkanHalaman();
});

tombolKanan.addEventListener("click", function () {
  halamanSekarang = halamanSekarang + 1;
  tampilkanHalaman();
});

// Kalau layar diputar (landscape/portrait) atau ukurannya berubah, hitung ulang
window.addEventListener("resize", tampilkanHalaman);

// Tampilkan halaman pertama saat halaman dibuka
tampilkanHalaman();

// Ringkasan isi daftar (alamat + teks tiap kartu), untuk mendeteksi ada tidaknya perubahan
function ringkasan(wadah) {
  var hasil = "";
  var tautan = wadah.querySelectorAll("a");
  for (var i = 0; i < tautan.length; i++) {
    hasil = hasil + tautan[i].getAttribute("href") + tautan[i].textContent.trim() + ";";
  }
  return hasil;
}

// ---------- 5. Perbarui daftar room tiap 5 detik ----------
function perbaruiDaftar() {
  // Jangan bekerja kalau tab sedang tidak dilihat (hemat server)
  if (document.hidden) {
    return;
  }

  // Ambil ulang halaman ini dari server di belakang layar
  fetch(window.location.href, { cache: "no-store" })
    .then(function (jawaban) {
      return jawaban.text();
    })
    .then(function (teksHalaman) {
      // Ubah teks HTML menjadi dokumen, lalu ambil daftar room yang baru
      var dokumenBaru = new DOMParser().parseFromString(teksHalaman, "text/html");
      var daftarBaru = dokumenBaru.getElementById("room-list");

      if (daftarBaru && ringkasan(daftarBaru) !== ringkasan(daftar)) {
        daftar.innerHTML = daftarBaru.innerHTML;
        tampilkanHalaman();   // atur ulang halaman dan panah
      }
    })
    .catch(function () {
      // Kalau gagal (misalnya internet putus), abaikan dan coba lagi 5 detik lagi
    });
}

setInterval(perbaruiDaftar, 5000);   // 5000 milidetik = 5 detik