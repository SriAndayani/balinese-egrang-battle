@extends('admin.dashboard')

{{-- $soal berisi data soal kalau sedang MENGUBAH, dan null kalau sedang MENAMBAH --}}
@php
    $mengubah = $soal !== null;
@endphp

@section('title', $mengubah ? 'Ubah Soal' : 'Tambah Soal')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Admin/soalForm.css') }}">

<div class="sf">

    {{-- Judul --}}
    <div class="sf-head">
        <span class="sf-head__icon" aria-hidden="true">
            @if ($mengubah)
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
            @else
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            @endif
        </span>
        <h1 class="sf-title">{{ $mengubah ? 'Ubah Soal' : 'Tambah Soal' }}</h1>
    </div>

    {{-- Ringkasan error. unique() menghapus pesan kembar (misalnya "semua jawaban harus diisi" yang muncul 4 kali) --}}
    @if ($errors->any())
        <div class="sf-error" role="alert">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v6"/><path d="M12 16.5v.5"/></svg>
            <div>
                @foreach (collect($errors->all())->unique() as $pesan)
                    <p>{{ $pesan }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- enctype="multipart/form-data" WAJIB kalau form mengirim file (gambar) --}}
    <form method="POST"
          enctype="multipart/form-data"
          action="{{ $mengubah ? route('admin.soal.update', $soal) : route('admin.soal.store') }}"
          class="sf-card">
        @csrf

        {{-- 1. Pertanyaan --}}
        <div class="sf-field">
            <label for="pertanyaan" class="sf-label"><span class="sf-num">1</span> Pertanyaan</label>
            <textarea id="pertanyaan" name="pertanyaan" rows="3" required maxlength="255"
                      placeholder="Tulis pertanyaan di sini"
                      class="sf-input sf-textarea">{{ old('pertanyaan', $soal?->pertanyaan) }}</textarea>
        </div>

        {{-- 2. Kategori --}}
        <div class="sf-field">
            <label for="id_kategori" class="sf-label"><span class="sf-num">2</span> Kategori</label>
            <div class="sf-select">
                <select id="id_kategori" name="id_kategori" required class="sf-input">
                    <option value="">-- Pilih kategori --</option>
                    @foreach ($daftarKategori as $kategori)
                        <option value="{{ $kategori->id_kategori }}"
                                @selected((string) old('id_kategori', $soal?->id_kategori) === (string) $kategori->id_kategori)>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </div>
            @if ($daftarKategori->isEmpty())
                <p class="sf-warn">
                    Belum ada kategori.
                    <a href="{{ route('admin.kategori.index') }}">Buat kategori dulu</a>.
                </p>
            @endif
        </div>

        {{-- 3. Gambar (opsional) --}}
        <div class="sf-field">
            <label for="gambar" class="sf-label">
                <span class="sf-num">3</span> Gambar <small>(opsional, maksimal 2 MB)</small>
            </label>

            @if ($mengubah && $soal->gambar)
                <div class="sf-gambar-lama">
                    <img src="{{ asset('storage/' . $soal->gambar) }}" alt="Gambar soal">
                    <label class="sf-hapus-gambar">
                        <input type="checkbox" name="hapus_gambar" value="1">
                        Hapus gambar ini
                    </label>
                </div>
            @endif

            <label class="sf-file" for="gambar">
                <span class="sf-file__btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 16l-5-5-9 9"/></svg>
                    Pilih gambar
                </span>
                <span class="sf-file__nama" id="nama-file">Belum ada file dipilih</span>
                <input type="file" id="gambar" name="gambar" accept="image/*">
            </label>

            @if ($mengubah && $soal->gambar)
                <p class="sf-note">Pilih file baru untuk mengganti gambar yang sekarang.</p>
            @endif
        </div>

        {{-- 4. Pilihan jawaban --}}
        <div class="sf-field">
            <p class="sf-label"><span class="sf-num">4</span> Pilihan jawaban</p>

            <div class="sf-opsi">
                @foreach (range(0, 3) as $i)
                    <div class="sf-opsi__row">
                        <label class="sf-opsi__pilih">
                            {{-- Bulatan untuk menandai jawaban yang benar. value = urutan (0-3) --}}
                            <input type="radio" name="benar" value="{{ $i }}" required
                                   aria-label="Tandai jawaban {{ chr(65 + $i) }} sebagai benar"
                                   @checked((string) old('benar', $indexBenar) === (string) $i)>
                            <span class="sf-opsi__dot" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                            </span>
                            <span class="sf-opsi__huruf">{{ chr(65 + $i) }}</span>
                        </label>

                        {{-- name="jawaban[0]" ... "jawaban[3]" : dikirim sebagai array --}}
                        <input type="text" name="jawaban[{{ $i }}]"
                               value="{{ old('jawaban.' . $i, $teks[$i] ?? '') }}"
                               placeholder="Jawaban {{ chr(65 + $i) }}" required
                               aria-label="Jawaban {{ chr(65 + $i) }}"
                               class="sf-input sf-opsi__input">

                        <span class="sf-opsi__benar">Benar</span>
                    </div>
                @endforeach
            </div>

            <p class="sf-note sf-note--icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6"/><path d="M12 7.5v.5"/></svg>
                Pilih bulatan di sebelah kiri untuk menandai jawaban yang benar.
            </p>
        </div>

        {{-- Tombol --}}
        <div class="sf-actions">
            <a href="{{ route('admin.soal.index') }}" class="sf-btn sf-btn--ghost">Batal</a>
            <button type="submit" class="sf-btn sf-btn--primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                Simpan
            </button>
        </div>
    </form>
</div>

{{-- Tampilkan nama file yang dipilih --}}
<script>
    document.getElementById('gambar')?.addEventListener('change', function () {
        document.getElementById('nama-file').textContent =
            this.files.length ? this.files[0].name : 'Belum ada file dipilih';
    });
</script>
@endsection
