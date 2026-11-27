// ---------- 1. Tombol layar penuh ----------
var tombolLayar = document.getElementById("btn-layar");

function layarPenuhDidukung() {
  return document.fullscreenEnabled || document.webkitFullscreenEnabled;
}

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
    peristiwa.preventDefault();   
    kolomB.focus();
  }
});

// ---------- 3. Cegah klik ganda pada tombol Mulai ----------
var formulir = document.getElementById("form-battle");
var tombolMulai = document.getElementById("btn-mulai");

formulir.addEventListener("submit", function () {
  setTimeout(function () {
    tombolMulai.disabled = true;
  }, 0);
});