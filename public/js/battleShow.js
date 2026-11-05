// public/js/battle-setup.js
// Tugas file ini:
//   1. Tombol layar penuh (pojok kanan bawah).
//   2. Tekan Enter di kolom Pemain 1 -> pindah ke kolom Pemain 2.
//   3. Mencegah tombol Mulai ditekan dua kali.

// ---------- 1. Tombol layar penuh ----------
var tombolLayar = document.getElementById("btn-layar");

// Apakah browser ini mendukung layar penuh? (iPhone tidak mendukung)
function layarPenuhDidukung() {
  return document.fullscreenEnabled || document.webkitFullscreenEnabled;
}

// Sedang dalam mode layar penuh atau tidak?
function sedangLayarPenuh() {
  return document.fullscreenElement || document.webkitFullscreenElement;
}

if (!layarPenuhDidukung()) {
  // Kalau tidak didukung, sembunyikan tombolnya
  tombolLayar.hidden = true;
} else {
  tombolLayar.addEventListener("click", function () {
    var halaman = document.documentElement;

    if (sedangLayarPenuh()) {
      // Keluar dari layar penuh
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else {
        document.webkitExitFullscreen();
      }
    } else {
      // Masuk ke layar penuh
      if (halaman.requestFullscreen) {
        halaman.requestFullscreen();
      } else {
        halaman.webkitRequestFullscreen();
      }
    }
  });
}

// ---------- 2. Enter di kolom A pindah ke kolom B ----------
var kolomA = document.getElementById("nama_a");
var kolomB = document.getElementById("nama_b");

kolomA.addEventListener("keydown", function (peristiwa) {
  if (peristiwa.key === "Enter") {
    peristiwa.preventDefault();   // jangan kirim formulir dulu
    kolomB.focus();
  }
});

// ---------- 3. Cegah klik ganda pada tombol Mulai ----------
var formulir = document.getElementById("form-battle");
var tombolMulai = document.getElementById("btn-mulai");

formulir.addEventListener("submit", function () {
  // Dimatikan sedikit terlambat agar formulir tetap terkirim
  setTimeout(function () {
    tombolMulai.disabled = true;
  }, 0);
});