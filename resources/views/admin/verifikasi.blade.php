@extends('admin.dashboard')

@section('title', 'Verifikasi Guru')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Admin/verifikasi.css') }}">

<div class="vg">

    {{-- Judul --}}
    <div class="vg-head">
        <span class="vg-head__icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3.1-7 7-7 1.6 0 3 .4 4.2 1.2"/><path d="M16 18l2 2 4-4"/></svg>
        </span>
        <h1 class="vg-title">Verifikasi Guru</h1>
    </div>

    {{-- ============ TAB FILTER ============
         Daftar tab dibuat dari array, sama seperti menu sidebar.
         key   : nilai yang dikirim lewat alamat (?status=...)
         label : tulisan di tab
         total : angka di dalam kurung
    --}}
    @php
        $tabs = [
            ['key' => 'semua',   'label' => 'semua',         'total' => $jumlah['semua']],
            ['key' => 'pending', 'label' => 'menunggu',      'total' => $jumlah['pending']],
            ['key' => 'active',  'label' => 'terverifikasi', 'total' => $jumlah['active']],
        ];
    @endphp

    <nav class="vg-tabs" aria-label="Filter status">
        @foreach ($tabs as $tab)
            <a href="{{ route('admin.verifikasi.index', ['status' => $tab['key']]) }}"
               class="vg-tab {{ $filter === $tab['key'] ? 'is-active' : '' }}"
               @if ($filter === $tab['key']) aria-current="page" @endif>
                {{ $tab['label'] }}
                <span class="vg-tab__count">{{ $tab['total'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- ============ DAFTAR CARD GURU ============ --}}
    <div class="vg-list">

        @forelse ($daftarGuru as $guru)
            <article class="vg-card">

                {{-- Baris atas: avatar + identitas (kiri), badge status (kanan) --}}
                <div class="vg-card__top">
                    <div class="vg-identitas">
                        <span class="vg-avatar" aria-hidden="true">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($guru->username, 0, 1)) }}
                        </span>
                        <div class="vg-identitas__teks">
                            <p class="vg-nama">{{ $guru->username }}</p>
                            <p class="vg-email">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
                                {{ $guru->email }}
                            </p>
                            @if ($guru->created_at)
                                <p class="vg-tanggal">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18"/><path d="M8 3v4"/><path d="M16 3v4"/></svg>
                                    Mendaftar {{ $guru->created_at->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Badge status --}}
                    @if ($guru->status === 'active')
                        <span class="vg-badge vg-badge--active"><span class="vg-dot"></span>Active</span>
                    @else
                        <span class="vg-badge vg-badge--pending"><span class="vg-dot"></span>Menunggu</span>
                    @endif
                </div>

                {{-- Tombol aksi --}}
                <div class="vg-card__aksi">
                    @if ($guru->status !== 'active')
                        {{-- Terima --}}
                        <form method="POST" action="{{ route('admin.verifikasi.terima', $guru) }}">
                            @csrf
                            <button type="submit" class="vg-btn vg-btn--terima">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                                Terima
                            </button>
                        </form>

                        {{-- Tolak (ada konfirmasi dulu karena data akan dihapus) --}}
                        <form method="POST" action="{{ route('admin.verifikasi.tolak', $guru) }}"
                              onsubmit="return confirm('Tolak dan hapus pendaftaran {{ $guru->username }}?')">
                            @csrf
                            <button type="submit" class="vg-btn vg-btn--hapus">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12"/><path d="M18 6L6 18"/></svg>
                                Tolak
                            </button>
                        </form>
                    @else
                        {{-- Guru sudah aktif: hapus akun --}}
                        <form method="POST" action="{{ route('admin.user.destroy', $guru) }}"
                              onsubmit="return confirm('Yakin ingin menghapus akun guru {{ $guru->username }}? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            <button type="submit" class="vg-btn vg-btn--hapus">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 14h10l1-14"/></svg>
                                Hapus Akun
                            </button>
                        </form>
                    @endif
                </div>
            </article>

        @empty
            {{-- Tampil kalau tidak ada guru pada filter ini --}}
            <div class="vg-empty">
                Tidak ada guru pada kategori ini.
            </div>
        @endforelse

    </div>
</div>
@endsection
