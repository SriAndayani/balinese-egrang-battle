@extends('guru.dashboard')

{{-- $room berisi data room kalau sedang MELIHAT DETAIL / MENGUBAH, dan null kalau sedang MENAMBAH --}}
@php
    $mengubah = $room !== null;
@endphp

@section('title', $mengubah ? 'Detail Room' : 'Tambah Room')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800">{{ $mengubah ? 'Detail Room' : 'Tambah Room' }}</h1>

    {{-- Ringkasan error --}}
    @if ($errors->any())
        <div class="mt-4 max-w-3xl p-3 rounded-lg bg-red-50 text-red-600 text-sm">
            @foreach (collect($errors->all())->unique() as $pesan)
                <p>{{ $pesan }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST"
          action="{{ $mengubah ? route('guru.room.update', $room) : route('guru.room.store') }}"
          class="mt-6 max-w-3xl bg-white rounded-xl border border-gray-200 p-6 space-y-5">
        @csrf

        {{-- Nama room --}}
        <div>
            <label for="nama_room" class="block text-sm text-gray-600 mb-1">Nama Room</label>
            <input type="text" id="nama_room" name="nama_room" required
                   value="{{ old('nama_room', $room?->nama_room) }}"
                   placeholder="Contoh: Latihan 1"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- Mode + Poin (2 kolom) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="mode" class="block text-sm text-gray-600 mb-1">Mode</label>
                <select id="mode" name="mode" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">-- Pilih mode --</option>
                    <option value="assessment" @selected(old('mode', $room?->mode) === 'assessment')>Assessment</option>
                    <option value="battle"     @selected(old('mode', $room?->mode) === 'battle')>Battle</option>
                </select>
            </div>

            <div>
                <label for="reward" class="block text-sm text-gray-600 mb-1">Poin Reward (opsional)</label>
                <input type="number" id="reward" name="reward" min="0" max="10000"
                       value="{{ old('reward', $room?->reward) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>

        {{-- Kode room: HANYA untuk mode assessment (hasilnya tercatat di "Hasil Siswa").
             Blok ini disembunyikan oleh JavaScript di bawah kalau mode = battle. --}}
        <div id="blok-kode">
            <label for="kode_room" class="block text-sm text-gray-600 mb-1">Kode Room</label>
            <div class="flex gap-2">
                <input type="text" id="kode_room" name="kode_room" maxlength="10"
                       value="{{ old('kode_room', $room?->kode_room) }}"
                       placeholder="Contoh: KELAS7A"
                       autocomplete="off"
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono
                              uppercase tracking-widest
                              focus:outline-none focus:ring-2 focus:ring-blue-400">
                <button type="button" id="btn-kode-baru"
                        class="px-3 py-2 rounded-lg text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 transition">
                    Buat ulang
                </button>
                <button type="button" id="btn-salin-kode"
                        class="px-3 py-2 rounded-lg text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 transition">
                    Salin
                </button>
            </div>
            <p class="text-xs text-gray-400 mt-1">
                4 sampai 10 huruf atau angka tanpa spasi. Bagikan kode ini ke siswa; mereka memasukkannya saat memainkan room ini.
            </p>
        </div>

        {{-- Status aktif.
             Kalau form baru saja gagal divalidasi, pakai isian terakhir (old);
             kalau tidak, pakai status room (atau aktif untuk room baru). --}}
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="status" value="1" class="w-4 h-4"
                   @checked($errors->any() ? old('status') : ($room?->status ?? true))>
            Room aktif (siswa bisa memainkannya)
        </label>

        {{-- Pilihan soal --}}
        <div>
            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                <p class="text-sm text-gray-600">Pilih soal untuk room ini</p>
                <input type="text" id="cari-soal" placeholder="Cari soal..."
                       class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="max-h-80 overflow-y-auto border border-gray-200 rounded-lg divide-y divide-gray-200">
                @forelse ($daftarSoal as $soal)
                    {{-- data-teks dipakai oleh kotak "Cari soal..." di bawah --}}
                    <div class="soal-item" data-teks="{{ \Illuminate\Support\Str::lower(
                                                $soal->pertanyaan . ' ' .
                                                ($soal->kategori?->nama_kategori ?? '')
                                                ($soal->pembuat?->role === 'admin' ? 'soal admin' : 'Soal saya')
                                            ) }}">
                        <label class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="soal[]" value="{{ $soal->id_soal }}" class="mt-1 w-4 h-4"
                                   @checked(in_array($soal->id_soal, old('soal', $terpilih)))>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800">{{ $soal->pertanyaan }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $soal->kategori?->nama_kategori ?? 'Tanpa kategori' }}
                                    ·
                                    {{ $soal->pembuat?->role === 'admin' ? 'Soal admin' : 'Soal saya' }}
                                </p>
                            </div>
                        </label>
                    </div>
                @empty
                    <p class="px-4 py-6 text-center text-sm text-gray-500">
                        Belum ada soal yang tersedia.
                    </p>
                @endforelse
            </div>

            <p class="text-xs text-gray-400 mt-2">
                Yang tampil: soal buatanmu dan soal dari admin. Soal guru lain tidak terlihat.
            </p>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
            <a href="{{ route('guru.room.index') }}"
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