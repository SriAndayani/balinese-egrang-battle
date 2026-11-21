@extends('guru.dashboard')

@section('title', 'Kelola Room')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Guru/kelolaRoom.css') }}">

<div class="rf rf--wide">

    {{-- ============ JUDUL + TOMBOL TAMBAH ============ --}}
    <div class="kr-top">
        <div class="rf-head">
            <span class="rf-head__icon" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21h16"/><path d="M6 21V4l9-1v18"/><path d="M15 5h3v16"/><path d="M11.5 12v1"/></svg>
            </span>
            <h1 class="rf-title">Kelola Room</h1>
        </div>

        <a href="{{ route('guru.room.create') }}" class="rf-btn rf-btn--primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            Tambahkan Room
        </a>
    </div>

    {{-- Pesan error (misalnya room gagal dihapus) --}}
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

    {{-- ============ TABEL ROOM ============ --}}
    <div class="kr-card">
        <table class="kr-table">
            <thead>
                <tr>
                    <th class="kr-col-no">#</th>
                    <th>Nama Room</th>
                    <th>Kode Room</th>
                    <th class="kr-center">Status</th>
                    <th class="kr-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($daftarRoom as $room)
                    <tr class="kr-row">
                        <td>
                            <span class="kr-no">{{ $daftarRoom->firstItem() + $loop->index }}</span>
                        </td>

                        <td>
                            <p class="kr-nama">{{ $room->nama_room }}</p>
                            <p class="kr-meta">
                                <span class="rf-tag {{ $room->mode === 'assessment' ? 'kr-tag--assessment' : 'kr-tag--battle' }}">{{ ucfirst($room->mode) }}</span>
                                <span class="rf-tag rf-tag--sumber">{{ $room->soals_count }} soal</span>
                            </p>
                        </td>

                        <td>
                            @if ($room->mode === 'assessment')
                                <span class="kr-kode">{{ $room->kode_room ?? '-' }}</span>
                            @else
                                <span class="kr-tanpa-kode">Tanpa kode</span>
                            @endif
                        </td>

                        <td class="kr-center">
                            @if ($room->status)
                                <span class="kr-status kr-status--aktif"><span class="kr-dot"></span>Aktif</span>
                            @else
                                <span class="kr-status kr-status--off"><span class="kr-dot"></span>Tidak aktif</span>
                            @endif
                        </td>

                        <td>
                            <div class="kr-aksi">
                                <a href="{{ route('guru.room.edit', $room) }}" class="rf-btn rf-btn--lihat">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Detail
                                </a>

                                <form method="POST" action="{{ route('guru.room.destroy', $room) }}"
                                      onsubmit="return confirm('Hapus room {{ $room->nama_room }}?')">
                                    @csrf
                                    <button type="submit" title="Hapus room" class="rf-btn kr-btn-hapus"
                                            aria-label="Hapus room {{ $room->nama_room }}">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 14h10l1-14"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="kr-empty">
                            Belum ada room. Klik "Tambahkan Room" untuk membuat yang pertama.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Nomor halaman --}}
    <div class="kr-pagination">
        {{ $daftarRoom->links() }}
    </div>
</div>
@endsection
