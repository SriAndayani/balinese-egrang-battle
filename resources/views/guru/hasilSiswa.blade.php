@extends('guru.dashboard')

@section('title', 'Hasil Siswa')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Guru/hasilSiswa.css') }}">

<div class="rf">

    {{-- Judul --}}
    <div class="rf-head">
        <span class="rf-head__icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M8 17v-6"/><path d="M12 17V7"/><path d="M16 17v-4"/></svg>
        </span>
        <h1 class="rf-title">Hasil Siswa</h1>
    </div>

    @if ($errors->any())
        <div class="rf-error" role="alert">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v6"/><path d="M12 16.5v.5"/></svg>
            <div>
                @foreach ($errors->all() as $pesan)
                    <p>{{ $pesan }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============ DAFTAR ROOM ============ --}}
    @if ($daftarRoom->count())
        <div class="hs-grid">
            @foreach ($daftarRoom as $room)
                <article class="hs-card">
                    <div class="hs-card__head">
                        <h2 class="hs-card__nama">{{ $room->nama_room }}</h2>

                        <div class="kr-aksi">
                            <form method="POST" action="{{ route('guru.hasil.destroy', $room) }}"
                                  onsubmit="return confirm('Hapus SEMUA hasil siswa di room {{ $room->nama_room }}? Tindakan ini tidak bisa dibatalkan.')">
                                @csrf
                                <button type="submit" class="rf-btn kr-btn-hapus"
                                        aria-label="Hapus semua hasil siswa di room {{ $room->nama_room }}">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 14h10l1-14"/></svg>
                                    Hapus
                                </button>
                            </form>

                            <a href="{{ route('guru.hasil.show', $room) }}" class="rf-btn rf-btn--primary hs-btn-detail">
                                Detail
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="hs-card__body">
                        <div class="hs-kode">
                            <p class="rf-overline">Kode Room</p>
                            <span class="kr-kode">{{ $room->kode_room ?? '-' }}</span>
                        </div>

                        <div class="hs-stats">
                            <div class="hs-stat hs-stat--siswa">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                <span><strong>{{ $jumlahSiswa[$room->id_room] ?? 0 }}</strong><small>siswa</small></span>
                            </div>
                            <div class="hs-stat hs-stat--soal">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6"/><path d="M9 12h6"/><path d="M9 16h3"/></svg>
                                <span><strong>{{ $room->soals_count }}</strong><small>soal</small></span>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="kr-empty hs-empty">
            Belum ada room assessment. Buat dulu lewat menu Kelola Room.
        </div>
    @endif

    {{-- Nomor halaman --}}
    <div class="kr-pagination">
        {{ $daftarRoom->links() }}
    </div>
</div>
@endsection
