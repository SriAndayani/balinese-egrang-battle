 const kotakCari = document.getElementById('cari-soal');

 kotakCari.addEventListener('input', function () {
     const kata = this.value.toLowerCase();
     document.querySelectorAll('.soal-item').forEach(function (item) {
         item.classList.toggle('hidden', !item.dataset.teks.includes(kata));
     });
 });

 // Tekan Enter di kotak pencarian jangan sampai mengirim form
 kotakCari.addEventListener('keydown', function (e) {
     if (e.key === 'Enter') e.preventDefault();
 });

 // ===== Kode room =====
 const pilihMode = document.getElementById('mode');
 const blokKode  = document.getElementById('blok-kode');
 const inputKode = document.getElementById('kode_room');
 const hurufKode = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';   // tanpa 0/O dan 1/I

 // Membuat kode acak 6 karakter
 function buatKode() {
     const acak = new Uint32Array(6);
     crypto.getRandomValues(acak);
     return Array.from(acak, n => hurufKode[n % hurufKode.length]).join('');
 }

 // Kolom kode hanya tampil untuk mode assessment
 function aturKode() {
     const assessment = pilihMode.value === 'assessment';
     blokKode.classList.toggle('hidden', !assessment);
     inputKode.required = assessment;

     // Kolom masih kosong: isi otomatis dengan kode acak (guru boleh menggantinya)
     if (assessment && inputKode.value === '') {
         inputKode.value = buatKode();
     }
 }

 pilihMode.addEventListener('change', aturKode);

 document.getElementById('btn-kode-baru').addEventListener('click', function () {
     inputKode.value = buatKode();
 });

 // Hanya huruf besar dan angka yang boleh diketik
 inputKode.addEventListener('input', function () {
     this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
 });

 // Tombol salin
 document.getElementById('btn-salin-kode').addEventListener('click', function () {
     navigator.clipboard.writeText(inputKode.value);
     const tombol = this;
     tombol.textContent = 'Tersalin!';
     setTimeout(function () { tombol.textContent = 'Salin'; }, 1500);
 });

 aturKode();