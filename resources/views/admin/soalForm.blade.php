@extends('admin.dashboard')

{{-- $soal berisi data soal kalau sedang MENGUBAH, dan null kalau sedang MENAMBAH --}}
@php
    $mengubah = $soal !== null;
@endphp

@section('title', $mengubah ? 'Ubah Soal' : 'Tambah Soal')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800">{{ $mengubah ? 'Ubah Soal' : 'Tambah Soal' }}</h1>

    {{-- Ringkasan error. unique() menghapus pesan kembar (misalnya "semua jawaban harus diisi" yang muncul 4 kali) --}}
    @if ($errors->any())
        <div class="mt-4 max-w-3xl p-3 rounded-lg bg-red-50 text-red-600 text-sm">
            @foreach (collect($errors->all())->unique() as $pesan)
                <p>{{ $pesan }}</p>
            @endforeach
        </div>
    @endif

    {{-- enctype="multipart/form-data" WAJIB kalau form mengirim file (gambar) --}}
    <form method="POST"
          enctype="multipart/form-data"
          action="{{ $mengubah ? route('admin.soal.update', $soal) : route('admin.soal.store') }}"
          class="mt-6 max-w-3xl bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        @csrf

        {{-- Pertanyaan --}}
        <div>
            <label for="pertanyaan" class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
            <textarea id="pertanyaan" name="pertanyaan" rows="3" required maxlength="255"
                      placeholder="Tulis pertanyaan di sini"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                             focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('pertanyaan', $soal?->pertanyaan) }}</textarea>
        </div>

        {{-- Kategori --}}
        <div>
            <label for="id_kategori" class="block text-sm text-gray-600 mb-1">Kategori</label>
            <select id="id_kategori" name="id_kategori" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">-- Pilih kategori --</option>
                @foreach ($daftarKategori as $kategori)
                    <option value="{{ $kategori->id_kategori }}"
                            @selected((string) old('id_kategori', $soal?->id_kategori) === (string) $kategori->id_kategori)>
                        {{ $kategori->nama_kategori }}
                    </option>
                @endforeach
            </select>
            @if ($daftarKategori->isEmpty())
                <p class="text-xs text-red-500 mt-1">
                    Belum ada kategori.
                    <a href="{{ route('admin.kategori.index') }}" class="underline">Buat kategori dulu</a>.
                </p>
            @endif
        </div>

        {{-- Gambar (opsional) --}}
        <div>
            <label for="gambar" class="block text-sm text-gray-600 mb-1">Gambar (opsional, maksimal 2 MB)</label>

            @if ($mengubah && $soal->gambar)
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ asset('storage/' . $soal->gambar) }}" alt="Gambar soal"
                         class="h-20 rounded-lg border border-gray-200">
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="hapus_gambar" value="1">
                        Hapus gambar ini
                    </label>
                </div>
            @endif

            <input type="file" id="gambar" name="gambar" accept="image/*"
                   class="block w-full text-sm text-gray-600
                          file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0
                          file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            @if ($mengubah && $soal->gambar)
                <p class="text-xs text-gray-400 mt-1">Pilih file baru untuk mengganti gambar yang sekarang.</p>
            @endif
        </div>

        {{-- Pilihan jawaban --}}
        <div>
            <p class="text-sm text-gray-600 mb-2">Pilihan jawaban</p>

            <div class="space-y-2">
                @foreach (range(0, 3) as $i)
                    <div class="flex items-center gap-3">
                        {{-- Bulatan untuk menandai jawaban yang benar. value = urutan (0-3) --}}
                        <input type="radio" name="benar" value="{{ $i }}" required
                               @checked((string) old('benar', $indexBenar) === (string) $i)
                               class="w-4 h-4">

                        <span class="w-5 text-sm font-medium text-gray-500">{{ chr(65 + $i) }}</span>

                        {{-- name="jawaban[0]" ... "jawaban[3]" : dikirim sebagai array --}}
                        <input type="text" name="jawaban[{{ $i }}]"
                               value="{{ old('jawaban.' . $i, $teks[$i] ?? '') }}"
                               placeholder="Jawaban {{ chr(65 + $i) }}" required
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                @endforeach
            </div>

            <p class="text-xs text-gray-400 mt-2">
                Pilih bulatan di sebelah kiri untuk menandai jawaban yang benar.
            </p>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
            <a href="{{ route('admin.soal.index') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 hover:bg-gray-300
                      text-gray-700 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-5 py-2 rounded-lg text-sm font-medium bg-blue-500 hover:bg-blue-600
                           text-white transition">
                Simpan
            </button>
        </div>
    </form>

@endsection