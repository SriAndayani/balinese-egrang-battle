function launchConfetti() {
    
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
        return;
    }

    const warna = ['#ff6b6b', '#ffd93d', '#6bcb77', '#4d96ff', '#ff922b', '#cc5de8', '#f8f9fa', '#74c0fc'];

    for (let i = 0; i < 80; i++) {
        setTimeout(() => {
            const kepingan = document.createElement('div');
            kepingan.className = 'confetti-piece';
            kepingan.style.left       = Math.random() * 100 + 'vw';
            kepingan.style.top        = '-20px';
            kepingan.style.background = warna[Math.floor(Math.random() * warna.length)];
            kepingan.style.width      = (Math.random() * 10 + 8) + 'px';
            kepingan.style.height     = (Math.random() * 10 + 8) + 'px';
            kepingan.style.animationDuration = (Math.random() * 2 + 2) + 's';
            kepingan.style.animationDelay    = Math.random() * 0.5 + 's';
            document.body.appendChild(kepingan);

            // Hapus setelah animasi selesai
            setTimeout(() => kepingan.remove(), 4000);
        }, i * 30);
    }
}

var tombolUnduh = document.getElementById("btn-unduh");
var kartuSkor   = document.getElementById("score-card");

// ---------- 1. Nama file yang aman ----------
function namaFileAman(teks) {
  var bersih = teks
    .trim()
    .replace(/[^\p{L}\p{N}]+/gu, "-")   
    .replace(/^-+|-+$/g, "");           

  if (bersih === "") {
    return "siswa";
  }
  return bersih;
}

// ---------- 2. Unduh kartu sebagai gambar ----------
async function unduhKartu() {

  if (typeof html2canvas === "undefined") {
    alert("Fitur unduh belum siap. Periksa koneksi internet, lalu muat ulang halaman.");
    return;
  }

  var teksAwal = tombolUnduh.textContent;
  tombolUnduh.disabled = true;                      
  tombolUnduh.textContent = "Menyiapkan gambar...";

  try {

    await document.fonts.ready;

    var kanvas = await html2canvas(kartuSkor, {
      scale: 2,                       
      useCORS: true,
      backgroundColor: "#4a8df0",     
      windowWidth: 900,               
      windowHeight: 1400,             

      onclone: function (dokumenSalinan) {
        dokumenSalinan.body.classList.add("sedang-unduh");
        dokumenSalinan.getElementById("score-card").classList.add("mode-unduh");
      },
    });

    var berkas = await new Promise(function (selesai) {
      kanvas.toBlob(selesai, "image/jpeg", 0.92);
    });

    var alamat = URL.createObjectURL(berkas);
    var tautan = document.createElement("a");
    tautan.href = alamat;
    tautan.download = "hasil-kuis-" + namaFileAman(tombolUnduh.dataset.nama) + ".jpg";
    document.body.appendChild(tautan);
    tautan.click();
    tautan.remove();
    URL.revokeObjectURL(alamat);

  } catch (galat) {
    console.error("Gagal membuat gambar:", galat);
    alert("Gambar gagal dibuat. Coba lagi.");
  } finally {
    tombolUnduh.disabled = false;
    tombolUnduh.textContent = teksAwal;
  }
}

tombolUnduh.addEventListener("click", unduhKartu);

// ---------- 3. Confetti saat halaman dibuka ----------
launchConfetti();