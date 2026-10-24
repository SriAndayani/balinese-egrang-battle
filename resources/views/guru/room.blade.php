@extends('guru.dashboard')

@section('title', 'Kelola Room')

@section('content')

    {{-- ============ JUDUL + TOMBOL TAMBAH ============ --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Room</h1>

        <a href="{{ route('guru.room.create') }}"
           class="px-4 py-2 rounded-lg text-sm font-medium bg-blue-500 hover:bg-blue-600
                  text-white transition">
            + Tambahkan Room
        </a>
    </div>

    {{-- Pesan error (misalnya room gagal dihapus) --}}
    @if ($errors->any())
        <div class="mt-4 p-3 rounded-lg bg-red-50 text-red-600 text-sm">
            @foreach ($errors->all() as $pesan)
                <p>{{ $pesan }}</p>
            @endforeach
        </div>
    @endif

    {{-- ============ TABEL ROOM ============ --}}
    <div class="mt-6 bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-left">
                    <tr>
                        <th class="px-4 py-3 w-12 font-medium">#</th>
                        <th class="px-4 py-3 font-medium">Nama Room</th>
                        <th class="px-4 py-3 font-medium">Kode Room</th>
                        <th class="px-4 py-3 font-medium text-center">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($daftarRoom as $room)
                        <tr>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $daftarRoom->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $room->nama_room }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ ucfirst($room->mode) }} · {{ $room->soals_count }} soal
                                </p>
                            </td>

                            <td class="px-4 py-3">
                                @if ($room->mode === 'assessment')
                                    <span class="font-mono tracking-wider text-gray-700">{{ $room->kode_room ?? '-' }}</span>
                                @else
                                    <span class="text-xs text-gray-400">Tanpa kode</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($room->status)
                                    <span class="px-3 py-1 rounded-md text-xs font-medium
                                                 bg-green-100 text-green-700 border border-green-300">
                                        Aktif
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-md text-xs font-medium
                                                 bg-gray-100 text-gray-500 border border-gray-300">
                                        Tidak aktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('guru.room.edit', $room) }}"
                                       class="px-3 py-1.5 rounded-lg text-xs font-medium
                                              bg-blue-500 hover:bg-blue-600 text-white transition">
                                        Detail
                                    </a>

                                    <form method="POST" action="{{ route('guru.room.destroy', $room) }}"
                                          onsubmit="return confirm('Hapus room {{ $room->nama_room }}?')">
                                        @csrf
                                        <button type="submit" title="Hapus room"
                                                class="px-3 py-1.5 rounded-lg text-xs font-medium
                                                       bg-red-500 hover:bg-red-600 text-white transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                Belum ada room. Klik "Tambahkan Room" untuk membuat yang pertama.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Nomor halaman --}}
    <div class="mt-4">
        {{ $daftarRoom->links() }}
    </div>

@endsection