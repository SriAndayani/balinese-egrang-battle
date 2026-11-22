@extends('guru.dashboard')

@section('title', 'detail Room')

@section('content')

{{-- $room berisi data room kalau sedang MELIHAT DETAIL / MENGUBAH, dan null kalau sedang MENAMBAH --}}
@php
    $mengubah = $room !== null;

    // Nilai awal form (pakai isian terakhir kalau validasi gagal)
    $modeTerpilih = old('mode', $room?->mode);
    $soalTerpilih = array_map('intval', (array) old('soal', $terpilih));
    $statusAktif  = $errors->any() ? (bool) old('status') : (bool) ($room?->status ?? true);

    // Data lengkap tiap soal untuk pop up "Pratinjau Soal".
    // >>> SESUAIKAN nama kolom pilihan jawaban & kunci dengan tabel soal kamu <<<
    // (kalau kolomnya tidak ada, nilainya null dan pop up tetap jalan tanpa error)
    $dataSoal = collect($daftarSoal)->mapWithKeys(function ($soal) {

        $hurufPilihan = ['A', 'B', 'C', 'D'];

        $pilihan = [];
        $jawabanBenar = null;

        foreach ($soal->jawabans as $index => $jawaban) {

            $huruf = $hurufPilihan[$index] ?? null;

            if ($huruf === null) {
                continue;
            }

            $pilihan[$huruf] = $jawaban->teks_jawaban;

            if ($jawaban->is_correct) {
                $jawabanBenar = $huruf;
            }
        }

        return [$soal->id_soal => [
            'pertanyaan' => $soal->pertanyaan,
            'kategori'   => $soal->kategori?->nama_kategori ?? 'Tanpa kategori',
            'sumber'     => $soal->pembuat?->role === 'admin'
                                ? 'Soal admin'
                                : 'Soal saya',
            'pilihan'    => $pilihan,
            'jawaban'    => $jawabanBenar,
        ]];
    });
@endphp

@section('title', $mengubah ? 'Detail Room' : 'Tambah Room')

@section('content')

<div class="rf">

    {{-- Judul --}}
    <div class="rf-head">
        <span class="rf-head__icon" aria-hidden="true">
            @if ($mengubah)
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
            @else
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            @endif
        </span>
        <h1 class="rf-title">{{ $mengubah ? 'Detail Room' : 'Tambah Room' }}</h1>
    </div>

    {{-- Ringkasan error --}}
    @if ($errors->any())
        <div class="rf-error" role="alert">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v6"/><path d="M12 16.5v.5"/></svg>
            <div>
                @foreach (collect($errors->all())->unique() as $pesan)
                    <p>{{ $pesan }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" id="form-room"
          action="{{ $mengubah ? route('guru.room.update', $room) : route('guru.room.store') }}"
          class="rf-card">
        @csrf

        {{-- 1. Nama room --}}
        <div class="rf-field">
            <label for="nama_room" class="rf-label"><span class="rf-num">1</span> Nama Room</label>
            <input type="text" id="nama_room" name="nama_room" required
                   value="{{ old('nama_room', $room?->nama_room) }}"
                   placeholder="Contoh: Latihan 1"
                   class="rf-input">
        </div>

        {{-- 2. Mode + 3. Poin --}}
        <div class="rf-grid">
            <fieldset class="rf-field rf-fieldset">
                <legend class="rf-label"><span class="rf-num">2</span> Mode</legend>

                <div class="rf-mode">
                    <label class="rf-mode__item">
                        <input type="radio" name="mode" value="assessment" required
                               @checked($modeTerpilih === 'assessment')>
                        <span class="rf-mode__card">
                            <span class="rf-mode__icon" aria-hidden="true">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4V3h6v1"/><path d="M9 12.5l2 2 4-4"/></svg>
                            </span>
                            <span class="rf-mode__name">Assessment</span>
                        </span>
                    </label>

                    <label class="rf-mode__item">
                        <input type="radio" name="mode" value="battle"
                               @checked($modeTerpilih === 'battle')>
                        <span class="rf-mode__card">
                            <span class="rf-mode__icon" aria-hidden="true">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 17.5L3 6V3h3l11.5 11.5"/><path d="M13 19l6-6"/><path d="M16 16l4 4"/><path d="M19 21l2-2"/></svg>
                            </span>
                            <span class="rf-mode__name">Battle</span>
                        </span>
                    </label>
                </div>

                <p class="rf-hint" id="hint-mode" @if ($modeTerpilih) hidden @endif>-- Pilih mode --</p>
            </fieldset>

            <div class="rf-field">
                <label for="reward" class="rf-label">
                    <span class="rf-num">3</span> Poin Reward <small>(opsional)</small>
                </label>
                <div class="rf-reward">
                    <span class="rf-reward__coin" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3 6.5 7 .8-5.2 4.8 1.5 7L12 17.6 5.7 21.1l1.5-7L2 9.3l7-.8z"/></svg>
                    </span>
                    <input type="number" id="reward" name="reward" min="0" max="10000"
                           value="{{ old('reward', $room?->reward) }}"
                           class="rf-input rf-input--reward">
                </div>
            </div>
        </div>

        {{-- Kode room: HANYA untuk mode assessment (hasilnya tercatat di "Hasil Siswa").
             Blok ini disembunyikan oleh JavaScript kalau mode bukan assessment. --}}
        <div id="blok-kode" class="rf-kode" @if ($modeTerpilih !== 'assessment') hidden @endif>
            <label for="kode_room" class="rf-label">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="15" r="4"/><path d="M11 12l9-9"/><path d="M17 6l3 3"/></svg>
                Kode Room
            </label>
            <div class="rf-kode__row">
                <input type="text" id="kode_room" name="kode_room" maxlength="10"
                       value="{{ old('kode_room', $room?->kode_room) }}"
                       placeholder="Contoh: KELAS7A"
                       autocomplete="off"
                       class="rf-input rf-input--kode">
                <button type="button" id="btn-kode-baru" class="rf-btn rf-btn--soft">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
                    Buat ulang
                </button>
                <button type="button" id="btn-salin-kode" class="rf-btn rf-btn--soft">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                    <span>Salin</span>
                </button>
            </div>
            <p class="rf-note">
                4 sampai 10 huruf atau angka tanpa spasi. Bagikan kode ini ke siswa; mereka memasukkannya saat memainkan room ini.
            </p>
        </div>

        {{-- Status aktif --}}
        <label class="rf-status">
            <input type="checkbox" name="status" value="1" id="status" @checked($statusAktif)>
            <span class="rf-switch" aria-hidden="true"><span class="rf-switch__knob"></span></span>
            <span class="rf-status__text">
                <strong>Room aktif (siswa bisa memainkannya)</strong>
                <small class="rf-status__on">Siswa sudah bisa masuk ke room ini</small>
                <small class="rf-status__off">Room ditutup, siswa belum bisa bermain</small>
            </span>
        </label>

        {{-- 4. Pilihan soal --}}
        <div class="rf-field">
            <div class="rf-soal-head">
                <div class="rf-soal-head__left">
                    <p class="rf-label"><span class="rf-num">4</span> Pilih soal untuk room ini</p>
                    <span class="rf-badge"><span id="jumlah-dipilih">{{ count($soalTerpilih) }}</span>/10 dipilih</span>
                </div>
                <div class="rf-search">
                    <label for="cari-soal" class="rf-sr">Cari soal</label>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>
                    <input type="text" id="cari-soal" placeholder="Cari soal..." class="rf-input rf-input--search">
                </div>
            </div>

            <div class="rf-soal-list">
                @forelse ($daftarSoal as $soal)
                    @php
                        $namaKategori = $soal->kategori?->nama_kategori ?? 'Tanpa kategori';
                        $sumberSoal   = $soal->pembuat?->role === 'admin' ? 'Soal admin' : 'Soal saya';
                    @endphp
                    {{-- data-teks dipakai oleh kotak "Cari soal..." --}}
                    <div class="soal-item" data-teks="{{ \Illuminate\Support\Str::lower(
                                                $soal->pertanyaan . ' ' . $namaKategori . ' ' . $sumberSoal
                                            ) }}">
                        <label class="soal-item__label">
                            <input type="checkbox" name="soal[]" value="{{ $soal->id_soal }}"
                                   @checked(in_array((int) $soal->id_soal, $soalTerpilih))>
                            <span class="soal-item__box" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                            </span>
                            <span class="soal-item__body">
                                <span class="soal-item__teks">{{ $soal->pertanyaan }}</span>
                                <span class="soal-item__meta">
                                    <span class="rf-tag {{ $soal->kategori ? 'rf-tag--kategori' : 'rf-tag--kosong' }}">{{ $namaKategori }}</span>
                                    <span aria-hidden="true">·</span>
                                    <span class="rf-tag rf-tag--sumber">{{ $sumberSoal }}</span>
                                </span>
                            </span>
                        </label>
                        <button type="button" class="rf-btn rf-btn--lihat btn-lihat-soal"
                                data-id="{{ $soal->id_soal }}"
                                aria-label="Lihat soal lengkap: {{ \Illuminate\Support\Str::limit($soal->pertanyaan, 60) }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            Lihat
                        </button>
                    </div>
                @empty
                    <p class="rf-empty">Belum ada soal yang tersedia.</p>
                @endforelse
                <p class="rf-empty" id="soal-tidak-ketemu" hidden>Tidak ada soal yang cocok dengan pencarianmu.</p>
            </div>

            <p class="rf-note rf-note--warning" id="peringatan-soal" role="alert"
               @if (count($soalTerpilih) === 10) hidden @endif>
                Soal harus berjumlah 10. Saat ini baru <span id="jumlah-peringatan">{{ count($soalTerpilih) }}</span> soal yang dipilih.
            </p>
        </div>

        {{-- Tombol --}}
        <div class="rf-actions">
            <a href="{{ route('guru.room.index') }}" class="rf-btn rf-btn--ghost">Batal</a>
            <button type="submit" class="rf-btn rf-btn--primary" id="btn-simpan-room"
                    @disabled(count($soalTerpilih) !== 10)>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                Simpan
            </button>
        </div>
    </form>
</div>

{{-- ============ POP UP PRATINJAU SOAL ============ --}}
<div class="rf-modal" id="modal-soal" hidden>
    <div class="rf-modal__backdrop" data-tutup-modal></div>
    <div class="rf-modal__box" role="dialog" aria-modal="true" aria-labelledby="judul-preview">
        <div class="rf-modal__head">
            <div class="rf-modal__title">
                <span class="rf-head__icon rf-head__icon--sm" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                </span>
                <div>
                    <h2 id="judul-preview">Pratinjau Soal</h2>
                    <small>Soal <span id="pv-urutan">1</span> dari <span id="pv-total">1</span></small>
                </div>
            </div>
            <button type="button" class="rf-modal__close" data-tutup-modal aria-label="Tutup pratinjau">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12"/><path d="M18 6L6 18"/></svg>
            </button>
        </div>

        <div class="rf-modal__body">
            <div class="soal-item__meta">
                <span class="rf-tag" id="pv-kategori"></span>
                <span class="rf-tag rf-tag--sumber" id="pv-sumber"></span>
                <span class="rf-tag rf-tag--dipilih" id="pv-dipilih" hidden>Sudah dipilih</span>
            </div>

            <div class="rf-pv-soal">
                <p class="rf-overline">Pertanyaan</p>
                <p class="rf-pv-soal__teks" id="pv-pertanyaan"></p>
            </div>

            <div class="rf-pv-opsi-head">
                <p class="rf-overline">Pilihan jawaban</p>
                <button type="button" class="rf-btn rf-btn--chip" id="btn-kunci" aria-pressed="true">Sembunyikan kunci</button>
            </div>
            <ol class="rf-pv-opsi" id="pv-opsi"></ol>
        </div>

        <div class="rf-modal__foot">
            <div class="rf-modal__nav">
                <button type="button" class="rf-btn rf-btn--round" id="pv-prev" aria-label="Soal sebelumnya">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button type="button" class="rf-btn rf-btn--round" id="pv-next" aria-label="Soal berikutnya">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                </button>
            </div>
            <div class="rf-modal__aksi">
                <button type="button" class="rf-btn rf-btn--primary" id="pv-pilih">Pilih soal ini</button>
            </div>
        </div>
    </div>
</div>

{{-- Data lengkap soal untuk pop up (dibaca oleh public/js/guruRoom.js) --}}
<script type="application/json" id="data-soal">@json($dataSoal)</script>

@endsection
