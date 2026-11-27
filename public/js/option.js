// ---------- 1. Ambil elemen yang dibutuhkan dari halaman ----------
var daftar      = document.getElementById("room-list"); // wadah semua kartu room
var tombolKiri  = document.getElementById("prev");      // panah kiri
var tombolKanan = document.getElementById("next");      // panah kanan

// Halaman yang sedang dilihat. 0 = halaman pertama.
var halamanSekarang = 0;

// ---------- 2. Berapa room yang tampil per halaman? ----------
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

  if (halamanSekarang > jumlahHalaman - 1) {
    halamanSekarang = jumlahHalaman - 1;
  }

  for (var i = 0; i < kartu.length; i++) {
    var nomorHalamanKartu = Math.floor(i / perHalaman);
    kartu[i].hidden = (nomorHalamanKartu !== halamanSekarang);
  }

  var adaBanyakHalaman = jumlahHalaman > 1;
  tombolKiri.style.visibility  = adaBanyakHalaman ? "visible" : "hidden";
  tombolKanan.style.visibility = adaBanyakHalaman ? "visible" : "hidden";

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


window.addEventListener("resize", tampilkanHalaman);

// Tampilkan halaman pertama saat halaman dibuka
tampilkanHalaman();

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
  if (document.hidden) {
    return;
  }

  fetch(window.location.href, { cache: "no-store" })
    .then(function (jawaban) {
      return jawaban.text();
    })
    .then(function (teksHalaman) {

      var dokumenBaru = new DOMParser().parseFromString(teksHalaman, "text/html");
      var daftarBaru = dokumenBaru.getElementById("room-list");

      if (daftarBaru && ringkasan(daftarBaru) !== ringkasan(daftar)) {
        daftar.innerHTML = daftarBaru.innerHTML;
        tampilkanHalaman();   
      }
    })
    .catch(function () {
    });
}

setInterval(perbaruiDaftar, 5000);   