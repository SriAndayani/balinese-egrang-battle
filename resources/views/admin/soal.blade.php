@extends('admin.dashboard')

@section('title', 'Kelola Soal')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Admin/soal.css') }}">

<div class="ks">

    {{-- ============ JUDUL + TOMBOL ATAS ============ --}}
    <div class="ks-top">
        <div class="ks-head">
            <span class="ks-head__icon" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4h7a3 3 0 0 1 3 3v13a2 2 0 0 0-2-2H2z"/><path d="M22 4h-7a3 3 0 0 0-3 3v13a2 2 0 0 1 2-2h8z"/></svg>
            </span>
            <h1 class="ks-title">Bank Soal</h1>
        </div>

        <div class="ks-top__aksi">
            <a href="{{ route('admin.kategori.index') }}" class="ks-btn ks-btn--ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 6h11"/><path d="M10 12h11"/><path d="M10 18h11"/><path d="M4 5l2 1-2 1"/><path d="M3.5 12h3"/><path d="M4 17l2 1-2 1"/></svg>
                Tambah Kategori
            </a>
            <a href="{{ route('admin.soal.create') }}" class="ks-btn ks-btn--primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Tambah Soal
            </a>
        </div>
    </div>

    {{-- Pesan error (misalnya soal gagal dihapus karena masih dipakai) --}}
    @if ($errors->any())
        <div class="ks-error" role="alert">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v6"/><path d="M12 16.5v.5"/></svg>
            <div>
                @foreach ($errors->all() as $pesan)
                    <p>{{ $pesan }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============ FILTER KATEGORI ============
         onchange="this.form.submit()" : begitu pilihan diganti, form langsung dikirim.
    --}}
    <form method="GET" action="{{ route('admin.soal.index') }}" class="ks-filter">
        <label for="filter-kategori" class="ks-sr">Filter kategori</label>
        <div class="ks-filter__box">
            <svg class="ks-filter__ikon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 5h18l-7 8v6l-4 2v-8z"/></svg>
            <select id="filter-kategori" name="kategori" onchange="this.form.submit()">
                <option value="">Semua kategori</option>
                @foreach ($daftarKategori as $kategori)
                    <option value="{{ $kategori->id_kategori }}"
                            @selected((string) $kategoriDipilih === (string) $kategori->id_kategori)>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <svg class="ks-filter__panah" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
        </div>
    </form>

    {{-- Warna badge kategori (dipilih berdasarkan id kategori, bergilir) --}}
    @php
        $warnaBadge = ['ks-badge--1', 'ks-badge--2', 'ks-badge--3', 'ks-badge--4'];
    @endphp

    {{-- ============ TABEL SOAL ============ --}}
    <section class="ks-card">
        <table class="ks-table">
            <thead>
                <tr>
                    <th class="ks-col-no">#</th>
                    <th>Pertanyaan</th>
                    <th>Kategori</th>
                    <th>Pembuat</th>
                    <th class="ks-center">Gambar</th>
                    <th class="ks-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($daftarSoal as $soal)
                    <tr class="ks-row">
                        {{-- Nomor lanjut antar halaman (halaman 2 mulai dari 11, dst.) --}}
                        <td>
                            <span class="ks-no">{{ $daftarSoal->firstItem() + $loop->index }}</span>
                        </td>

                        <td class="ks-pertanyaan">
                            {{ Str::limit($soal->pertanyaan, 80) }}
                        </td>

                        <td>
                            @if ($soal->kategori)
                                <span class="ks-badge {{ $warnaBadge[$soal->id_kategori % count($warnaBadge)] }}">
                                    {{ $soal->kategori->nama_kategori }}
                                </span>
                            @else
                                <span class="ks-strip">-</span>
                            @endif
                        </td>

                        <td>
                            <p class="ks-pembuat">{{ $soal->pembuat?->username ?? '-' }}</p>
                            <p class="ks-role">{{ ucfirst($soal->pembuat?->role ?? '') }}</p>
                        </td>

                        <td class="ks-center">
                            @if ($soal->gambar)
                                <a href="{{ asset('storage/' . $soal->gambar) }}" target="_blank"
                                   title="Lihat gambar" aria-label="Lihat gambar" class="ks-gambar">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 16l-5-5-9 9"/></svg>
                                </a>
                            @else
                                <span class="ks-strip">-</span>
                            @endif
                        </td>

                        {{-- @can memakai SoalPolicy: tombol hanya muncul kalau diizinkan --}}
                        <td>
                            <div class="ks-aksi">
                                @can('update', $soal)
                                    <a href="{{ route('admin.soal.edit', $soal) }}" class="ks-btn ks-btn--ubah">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                        Ubah
                                    </a>
                                @endcan

                                @can('delete', $soal)
                                    <form method="POST" action="{{ route('admin.soal.destroy', $soal) }}"
                                          onsubmit="return confirm('Hapus soal ini?')">
                                        @csrf
                                        <button type="submit" class="ks-btn ks-btn--hapus">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 14h10l1-14"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="ks-empty">
                            Belum ada soal pada pilihan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <p class="ks-note">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6"/><path d="M12 7.5v.5"/></svg>
        Soal buatan guru hanya bisa dihapus oleh admin (moderasi). Mengubah isinya hanya bisa dilakukan pembuatnya.
    </p>

    {{-- Nomor halaman --}}
    <div class="ks-pagination">
        {{ $daftarSoal->links() }}
    </div>
</div>
@endsection
