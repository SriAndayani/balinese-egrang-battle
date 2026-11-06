@extends('guru.dashboard')

@section('title', 'Hasil Siswa')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800">Hasil Siswa</h1>

    {{-- Pesan error (kalau ada) --}}
    @if ($errors->any())
        <div class="mt-4 p-3 rounded-lg bg-red-50 text-red-600 text-sm">
            @foreach ($errors->all() as $pesan)
                <p>{{ $pesan }}</p>
            @endforeach
        </div>
    @endif

    {{-- ============ DAFTAR ROOM (satu card per room) ============ --}}
    <div class="mt-6 flex flex-col gap-4 max-w-4xl">

        @forelse ($daftarRoom as $room)
            <div class="bg-white rounded-xl border border-gray-200 p-5">

                {{-- Baris atas: nama room (kiri), tombol (kanan) --}}
                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">{{ $room->nama_room }}</h2>

                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('guru.hasil.destroy', $room) }}"
                              onsubmit="return confirm('Hapus SEMUA hasil siswa di room {{ $room->nama_room }}? Tindakan ini tidak bisa dibatalkan.')">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-1.5 rounded-lg text-sm font-medium border border-gray-300
                                           text-gray-700 hover:bg-red-50 hover:border-red-300 hover:text-red-600 transition">
                                Hapus
                            </button>
                        </form>

                        <a href="{{ route('guru.hasil.show', $room) }}"
                           class="px-4 py-1.5 rounded-lg text-sm font-medium bg-blue-500 hover:bg-blue-600
                                  text-white transition">
                            Detail
                        </a>
                    </div>
                </div>

                {{-- Baris bawah: kode room (kiri), jumlah siswa dan soal (kanan) --}}
                <div class="flex flex-wrap items-end justify-between gap-3 mt-3">
                    <div>
                        <p class="text-xs text-gray-400">Kode Room</p>
                        <span class="inline-block mt-1 px-2 py-0.5 border border-gray-300 rounded
                                     font-mono text-sm tracking-wider text-gray-700">
                            {{ $room->kode_room ?? '-' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <span>👤 {{ $jumlahSiswa[$room->id_room] ?? 0 }} siswa</span>
                        <span>📝 {{ $room->soals_count }} soal</span>
                    </div>
                </div>
            </div>

        @empty
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-500 text-sm">
                Belum ada room assessment. Buat dulu lewat menu Kelola Room.
            </div>
        @endforelse

    </div>

    {{-- Nomor halaman --}}
    <div class="mt-4 max-w-4xl">
        {{ $daftarRoom->links() }}
    </div>

@endsection