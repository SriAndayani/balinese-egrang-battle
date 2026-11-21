/* =========================================================
   Form Tambah / Detail Room — Ngiring Malajah Basa Bali
   Lokasi: public/js/guruRoom.js  (sudah dipanggil di guru/dashboard.blade.php)
   Semua bagian aman dijalankan di halaman lain: kalau elemennya
   tidak ada, bagian itu dilewati.
   ========================================================= */
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-room');
    if (!form) return; // bukan halaman tambah/detail room

    /* ---------- 1. Mode: tampilkan kode room hanya untuk assessment ---------- */
    var radioMode = form.querySelectorAll('input[name="mode"]');
    var blokKode  = document.getElementById('blok-kode');
    var hintMode  = document.getElementById('hint-mode');

    function aturMode() {
        var dipilih = form.querySelector('input[name="mode"]:checked');
        var nilai = dipilih ? dipilih.value : '';
        if (blokKode) blokKode.hidden = nilai !== 'assessment';
        if (hintMode) hintMode.hidden = nilai !== '';
    }
    radioMode.forEach(function (r) { r.addEventListener('change', aturMode); });
    aturMode();

    /* ---------- 2. Kode room: huruf besar, buat ulang, salin ---------- */
    var inputKode = document.getElementById('kode_room');
    var btnBaru   = document.getElementById('btn-kode-baru');
    var btnSalin  = document.getElementById('btn-salin-kode');

    if (inputKode) {
        inputKode.addEventListener('input', function () {
            inputKode.value = inputKode.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    }
    if (btnBaru && inputKode) {
        btnBaru.addEventListener('click', function () {
            var huruf = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            var kode = '';
            for (var i = 0; i < 6; i++) kode += huruf.charAt(Math.floor(Math.random() * huruf.length));
            inputKode.value = kode;
        });
    }
    if (btnSalin && inputKode) {
        btnSalin.addEventListener('click', function () {
            var teks = inputKode.value.trim();
            if (!teks) return;
            var label = btnSalin.querySelector('span');
            var selesai = function () {
                if (!label) return;
                label.textContent = 'Tersalin!';
                setTimeout(function () { label.textContent = 'Salin'; }, 1500);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(teks).then(selesai);
            } else {
                inputKode.select();
                document.execCommand('copy');
                selesai();
            }
        });
    }

    /* ---------- 3. Daftar soal: hitung yang dipilih ---------- */
    var cekSoal = Array.prototype.slice.call(form.querySelectorAll('input[name="soal[]"]'));
    var jumlah  = document.getElementById('jumlah-dipilih');

    function hitungDipilih() {
        if (!jumlah) return;
        jumlah.textContent = cekSoal.filter(function (c) { return c.checked; }).length;
    }
    cekSoal.forEach(function (c) { c.addEventListener('change', hitungDipilih); });
    hitungDipilih();

    /* ---------- 4. Cari soal ---------- */
    var cari      = document.getElementById('cari-soal');
    var items     = Array.prototype.slice.call(document.querySelectorAll('.soal-item'));
    var tidakAda  = document.getElementById('soal-tidak-ketemu');

    if (cari) {
        cari.addEventListener('input', function () {
            var q = cari.value.trim().toLowerCase();
            var tampil = 0;
            items.forEach(function (item) {
                var cocok = (item.dataset.teks || '').indexOf(q) !== -1;
                item.hidden = !cocok;
                if (cocok) tampil++;
            });
            if (tidakAda) tidakAda.hidden = !(items.length && tampil === 0);
        });
        // Enter di kotak cari jangan sampai mengirim form
        cari.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') e.preventDefault();
        });
    }

    /* ---------- 5. Pop up pratinjau soal ---------- */
    var modal = document.getElementById('modal-soal');
    var sumberData = document.getElementById('data-soal');
    if (!modal || !sumberData) return;

    var dataSoal = {};
    try { dataSoal = JSON.parse(sumberData.textContent) || {}; } catch (e) { dataSoal = {}; }

    var urutanId = items.map(function (item) {
        var btn = item.querySelector('.btn-lihat-soal');
        return btn ? String(btn.dataset.id) : null;
    }).filter(Boolean);

    var el = {
        urutan:     document.getElementById('pv-urutan'),
        total:      document.getElementById('pv-total'),
        kategori:   document.getElementById('pv-kategori'),
        sumber:     document.getElementById('pv-sumber'),
        dipilih:    document.getElementById('pv-dipilih'),
        pertanyaan: document.getElementById('pv-pertanyaan'),
        opsi:       document.getElementById('pv-opsi'),
        kunci:      document.getElementById('btn-kunci'),
        prev:       document.getElementById('pv-prev'),
        next:       document.getElementById('pv-next'),
        pilih:      document.getElementById('pv-pilih')
    };

    var idAktif = null;
    var tampilKunci = true;
    var pemicuTerakhir = null;

    function cariCheckbox(id) {
        return form.querySelector('input[name="soal[]"][value="' + id + '"]');
    }

    // Jawaban benar boleh disimpan sebagai huruf ("A"/"a") atau sebagai teks jawabannya
    function apakahBenar(huruf, teks, jawaban) {
        if (jawaban === null || jawaban === undefined || jawaban === '') return false;
        var j = String(jawaban).trim();
        return j.toUpperCase() === huruf || j === String(teks).trim();
    }

    function buatOpsi(huruf, teks, benar) {
        var li = document.createElement('li');
        if (benar && tampilKunci) li.className = 'is-benar';

        var h = document.createElement('span');
        h.className = 'rf-pv-opsi__huruf';
        h.textContent = huruf;

        var t = document.createElement('span');
        t.className = 'rf-pv-opsi__teks';
        t.textContent = teks;

        var b = document.createElement('span');
        b.className = 'rf-pv-opsi__benar';
        b.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';
        b.appendChild(document.createTextNode('Jawaban benar'));

        li.appendChild(h);
        li.appendChild(t);
        li.appendChild(b);
        return li;
    }

    function render() {
        var d = dataSoal[idAktif];
        if (!d) return;
        var posisi = urutanId.indexOf(String(idAktif));
        var cek = cariCheckbox(idAktif);
        var sudahDipilih = !!(cek && cek.checked);

        el.urutan.textContent = posisi + 1;
        el.total.textContent = urutanId.length;

        el.kategori.textContent = d.kategori;
        el.kategori.className = 'rf-tag ' + (d.kategori === 'Tanpa kategori' ? 'rf-tag--kosong' : 'rf-tag--kategori');
        el.sumber.textContent = d.sumber;
        el.dipilih.hidden = !sudahDipilih;
        el.pertanyaan.textContent = d.pertanyaan;

        el.opsi.innerHTML = '';
        var pilihan = d.pilihan || {};
        var kunciHuruf = Object.keys(pilihan);
        if (kunciHuruf.length === 0) {
            var kosong = document.createElement('li');
            kosong.className = 'rf-pv-kosong';
            kosong.textContent = 'Soal ini belum punya pilihan jawaban.';
            el.opsi.appendChild(kosong);
        } else {
            kunciHuruf.forEach(function (huruf) {
                el.opsi.appendChild(buatOpsi(huruf, pilihan[huruf], apakahBenar(huruf, pilihan[huruf], d.jawaban)));
            });
        }

        el.kunci.textContent = tampilKunci ? 'Sembunyikan kunci' : 'Tampilkan kunci';
        el.kunci.setAttribute('aria-pressed', tampilKunci ? 'true' : 'false');

        el.prev.disabled = posisi <= 0;
        el.next.disabled = posisi >= urutanId.length - 1;

        el.pilih.textContent = sudahDipilih ? 'Batalkan pilihan' : 'Pilih soal ini';
        el.pilih.classList.toggle('rf-btn--primary', !sudahDipilih);
        el.pilih.classList.toggle('rf-btn--ghost', sudahDipilih);
        el.pilih.classList.toggle('rf-btn--batal', sudahDipilih);
    }

    function buka(id, pemicu) {
        if (!dataSoal[id]) return;
        idAktif = String(id);
        pemicuTerakhir = pemicu || null;
        render();
        modal.hidden = false;
        document.body.classList.add('rf-modal-open');
        modal.querySelector('.rf-modal__close').focus();
    }

    function tutup() {
        modal.hidden = true;
        document.body.classList.remove('rf-modal-open');
        if (pemicuTerakhir) pemicuTerakhir.focus();
    }

    function geser(arah) {
        var posisi = urutanId.indexOf(String(idAktif)) + arah;
        if (posisi >= 0 && posisi < urutanId.length) {
            idAktif = urutanId[posisi];
            render();
        }
    }

    document.querySelectorAll('.btn-lihat-soal').forEach(function (btn) {
        btn.addEventListener('click', function () { buka(btn.dataset.id, btn); });
    });
    modal.querySelectorAll('[data-tutup-modal]').forEach(function (n) {
        n.addEventListener('click', tutup);
    });
    el.prev.addEventListener('click', function () { geser(-1); });
    el.next.addEventListener('click', function () { geser(1); });
    el.kunci.addEventListener('click', function () { tampilKunci = !tampilKunci; render(); });
    el.pilih.addEventListener('click', function () {
        var cek = cariCheckbox(idAktif);
        if (!cek) return;
        cek.checked = !cek.checked;
        cek.dispatchEvent(new Event('change', { bubbles: true }));
        render();
    });

    // Esc menutup pop up (hanya saat pop up terbuka)
    modal.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') tutup();
    });
});
