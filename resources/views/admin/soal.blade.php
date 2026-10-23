@extends('admin.dashboard')

@section('title', 'Kelola Soal')

@section('content')

    {{-- ============ JUDUL + TOMBOL ATAS ============ --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Soal</h1>

        <div class="flex gap-2">
            <a href="{{ route('admin.kategori.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-white border border-gray-300
                      text-gray-700 hover:bg-gray-100 transition">
                Tambah Kategori
            </a>
            <a href="{{ route('admin.soal.create') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-500 hover:bg-blue-600
                      text-white transition">
                Tambah Soal
            </a>
        </div>
    </div>

    {{-- Pesan error (misalnya soal gagal dihapus karena masih dipakai) --}}
    @if ($errors->any())
        <div class="mt-4 p-3 rounded-lg bg-red-50 text-red-600 text-sm">
            @foreach ($errors->all() as $pesan)
                <p>{{ $pesan }}</p>
            @endforeach
        </div>
    @endif

    {{-- ============ FILTER KATEGORI ============
         onchange="this.form.submit()" : begitu pilihan diganti, form langsung dikirim.
    --}}
    <form method="GET" action="{{ route('admin.soal.index') }}" class="mt-4">
        <select name="kategori" onchange="this.form.submit()"
                class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                       focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">Semua kategori</option>
            @foreach ($daftarKategori as $kategori)
                <option value="{{ $kategori->id_kategori }}"
                        @selected((string) $kategoriDipilih === (string) $kategori->id_kategori)>
                    {{ $kategori->nama_kategori }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Warna badge kategori (dipilih berdasarkan id kategori, bergilir) --}}
    @php
        $warnaBadge = [
            'bg-blue-100 text-blue-700',
            'bg-pink-100 text-pink-700',
            'bg-green-100 text-green-700',
            'bg-purple-100 text-purple-700',
        ];
    @endphp

    {{-- ============ TABEL SOAL ============ --}}
    <div class="mt-4 bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-left">
                    <tr>
                        <th class="px-4 py-3 w-12 font-medium">#</th>
                        <th class="px-4 py-3 font-medium">Pertanyaan</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Pembuat</th>
                        <th class="px-4 py-3 font-medium text-center">Gambar</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($daftarSoal as $soal)
                        <tr>
                            {{-- Nomor lanjut antar halaman (halaman 2 mulai dari 11, dst.) --}}
                            <td class="px-4 py-3 text-gray-500">
                                {{ $daftarSoal->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3 text-gray-800 max-w-md">
                                {{ Str::limit($soal->pertanyaan, 80) }}
                            </td>

                            <td class="px-4 py-3">
                                @if ($soal->kategori)
                                    <span class="px-2.5 py-1 rounded-md text-xs font-medium
                                                 {{ $warnaBadge[$soal->id_kategori % count($warnaBadge)] }}">
                                        {{ $soal->kategori->nama_kategori }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <p class="text-gray-800">{{ $soal->pembuat?->username ?? '-' }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($soal->pembuat?->role ?? '') }}</p>
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($soal->gambar)
                                    <a href="{{ asset('storage/' . $soal->gambar) }}" target="_blank"
                                       title="Lihat gambar" class="text-lg">🖼️</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- @can memakai SoalPolicy: tombol hanya muncul kalau diizinkan --}}
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    @can('update', $soal)
                                        <a href="{{ route('admin.soal.edit', $soal) }}"
                                           class="px-3 py-1.5 rounded-lg text-xs font-medium
                                                  bg-yellow-400 hover:bg-yellow-500 text-white transition">
                                            Ubah
                                        </a>
                                    @endcan

                                    @can('delete', $soal)
                                        <form method="POST" action="{{ route('admin.soal.destroy', $soal) }}"
                                              onsubmit="return confirm('Hapus soal ini?')">
                                            @csrf
                                            <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-medium
                                                           bg-red-500 hover:bg-red-600 text-white transition">
                                                Hapus
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Belum ada soal pada pilihan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="mt-3 text-xs text-gray-400">
        Soal buatan guru hanya bisa dihapus oleh admin (moderasi). Mengubah isinya hanya bisa dilakukan pembuatnya.
    </p>

    {{-- Nomor halaman --}}
    <div class="mt-4">
        {{ $daftarSoal->links() }}
    </div>

@endsection