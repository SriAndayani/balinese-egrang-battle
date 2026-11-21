@extends('admin.dashboard')

@section('title', 'Kelola Kategori')

@section('content')
<link rel="stylesheet" href="{{ asset('css/Admin/kategori.css') }}">

<div class="kt">

    {{-- Judul --}}
    <div class="kt-head">
        <span class="kt-head__icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 6h11"/><path d="M10 12h11"/><path d="M10 18h11"/><path d="M4 5l2 1-2 1"/><path d="M3.5 12h3"/><path d="M4 17l2 1-2 1"/></svg>
        </span>
        <h1 class="kt-title">Kelola Kategori</h1>
    </div>

    {{-- ============ RINGKASAN ERROR ============
         Menampilkan semua pesan error (tambah, ubah, maupun hapus) di satu tempat.
    --}}
    @if ($errors->any())
        <div class="kt-error" role="alert">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v6"/><path d="M12 16.5v.5"/></svg>
            <div>
                @foreach ($errors->all() as $pesan)
                    <p>{{ $pesan }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ============ FORM TAMBAH KATEGORI ============ --}}
    <section class="kt-card">
        <h2 class="kt-card__title">
            <span class="kt-card__plus" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            </span>
            Tambah Kategori
        </h2>

        <form method="POST" action="{{ route('admin.kategori.store') }}" class="kt-tambah">
            @csrf
            <label for="nama_kategori_baru" class="kt-sr">Nama kategori baru</label>
            <input type="text" id="nama_kategori_baru" name="nama_kategori"
                   placeholder="Nama kategori baru"
                   required
                   class="kt-input">
            <button type="submit" class="kt-btn kt-btn--primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                Tambah
            </button>
        </form>
    </section>

    {{-- ============ DAFTAR KATEGORI ============ --}}
    <section class="kt-card kt-card--table">
        <table class="kt-table">
            <thead>
                <tr>
                    <th class="kt-col-no">No</th>
                    <th>Nama Kategori</th>
                    <th class="kt-right">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($daftarKategori as $kategori)
                    <tr class="kt-row" id="baris-{{ $kategori->id_kategori }}">
                        <td>
                            <span class="kt-no">{{ $loop->iteration }}</span>
                        </td>

                        {{-- Kolom nama: ada 2 tampilan (teks biasa / form ubah),
                             dipertukarkan oleh fungsi toggleUbah() di bawah --}}
                        <td>
                            <span id="nama-{{ $kategori->id_kategori }}" class="kt-nama">
                                {{ $kategori->nama_kategori }}
                            </span>

                            <form id="form-ubah-{{ $kategori->id_kategori }}" class="kt-hidden"
                                  method="POST"
                                  action="{{ route('admin.kategori.update', $kategori) }}">
                                @csrf
                                <div class="kt-ubah">
                                    <input type="text" name="nama_kategori"
                                           value="{{ $kategori->nama_kategori }}"
                                           aria-label="Ubah nama kategori {{ $kategori->nama_kategori }}"
                                           required
                                           class="kt-input kt-input--ubah">
                                    <button type="submit" class="kt-btn kt-btn--simpan">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>
                                        Simpan
                                    </button>
                                    <button type="button"
                                            onclick="toggleUbah({{ $kategori->id_kategori }})"
                                            class="kt-btn kt-btn--batal">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </td>

                        {{-- Kolom aksi --}}
                        <td>
                            <div class="kt-aksi">
                                <button type="button"
                                        onclick="toggleUbah({{ $kategori->id_kategori }})"
                                        class="kt-btn kt-btn--ubah">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                    Ubah
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.kategori.destroy', $kategori) }}"
                                      onsubmit="return confirm('Hapus kategori {{ $kategori->nama_kategori }}?')">
                                    @csrf
                                    <button type="submit" class="kt-btn kt-btn--hapus">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M6 6l1 14h10l1-14"/></svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="3" class="kt-empty">
                            Belum ada kategori. Tambahkan lewat form di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>

<script>
    // Menukar tampilan satu baris: teks biasa <-> form ubah.
    // "id" adalah id_kategori baris yang diklik.
    function toggleUbah(id) {
        document.getElementById('nama-' + id).classList.toggle('kt-hidden');
        var form = document.getElementById('form-ubah-' + id);
        form.classList.toggle('kt-hidden');
        document.getElementById('baris-' + id).classList.toggle('is-editing');
        if (!form.classList.contains('kt-hidden')) {
            form.querySelector('input[name="nama_kategori"]').focus();
        }
    }
</script>
@endsection
