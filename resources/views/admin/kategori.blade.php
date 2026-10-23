@extends('admin.dashboard')

@section('title', 'Kelola Kategori')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800">Kelola Kategori</h1>

    {{-- ============ RINGKASAN ERROR ============
         Menampilkan semua pesan error (tambah, ubah, maupun hapus) di satu tempat.
    --}}
    @if ($errors->any())
        <div class="mt-4 max-w-3xl p-3 rounded-lg bg-red-50 text-red-600 text-sm">
            @foreach ($errors->all() as $pesan)
                <p>{{ $pesan }}</p>
            @endforeach
        </div>
    @endif

    {{-- ============ FORM TAMBAH KATEGORI ============ --}}
    <div class="mt-6 max-w-3xl bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="font-semibold text-gray-800">Tambah Kategori</h2>

        <form method="POST" action="{{ route('admin.kategori.store') }}"
              class="mt-3 flex flex-col sm:flex-row gap-2">
            @csrf
            <input type="text" name="nama_kategori"
                   placeholder="Nama kategori baru"
                   required
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button type="submit"
                    class="px-5 py-2 rounded-lg text-sm font-medium
                           bg-blue-500 hover:bg-blue-600 text-white transition">
                Tambah
            </button>
        </form>
    </div>

    {{-- ============ DAFTAR KATEGORI ============ --}}
    <div class="mt-6 max-w-3xl bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-5 py-3 w-16 font-medium">No</th>
                    <th class="px-5 py-3 font-medium">Nama Kategori</th>
                    <th class="px-5 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($daftarKategori as $kategori)
                    <tr>
                        <td class="px-5 py-3 text-gray-500">{{ $loop->iteration }}</td>

                        {{-- Kolom nama: ada 2 tampilan (teks biasa / form ubah),
                             dipertukarkan oleh fungsi toggleUbah() di bawah --}}
                        <td class="px-5 py-3">
                            <span id="nama-{{ $kategori->id_kategori }}" class="text-gray-800">
                                {{ $kategori->nama_kategori }}
                            </span>

                            <form id="form-ubah-{{ $kategori->id_kategori }}" class="hidden"
                                  method="POST"
                                  action="{{ route('admin.kategori.update', $kategori) }}">
                                @csrf
                                <div class="flex items-center gap-2">
                                    <input type="text" name="nama_kategori"
                                           value="{{ $kategori->nama_kategori }}"
                                           required
                                           class="flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm
                                                  focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <button type="submit"
                                            class="px-3 py-1.5 rounded-lg text-xs font-medium
                                                   bg-green-500 hover:bg-green-600 text-white transition">
                                        Simpan
                                    </button>
                                    <button type="button"
                                            onclick="toggleUbah({{ $kategori->id_kategori }})"
                                            class="px-3 py-1.5 rounded-lg text-xs font-medium
                                                   bg-gray-200 hover:bg-gray-300 text-gray-700 transition">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </td>

                        {{-- Kolom aksi --}}
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                <button type="button"
                                        onclick="toggleUbah({{ $kategori->id_kategori }})"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium
                                               bg-yellow-400 hover:bg-yellow-500 text-white transition">
                                    Ubah
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.kategori.destroy', $kategori) }}"
                                      onsubmit="return confirm('Hapus kategori {{ $kategori->nama_kategori }}?')">
                                    @csrf
                                    <button type="submit"
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
                        <td colspan="3" class="px-5 py-8 text-center text-gray-500">
                            Belum ada kategori. Tambahkan lewat form di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        // Menukar tampilan satu baris: teks biasa <-> form ubah.
        // "id" adalah id_kategori baris yang diklik.
        function toggleUbah(id) {
            document.getElementById('nama-' + id).classList.toggle('hidden');
            document.getElementById('form-ubah-' + id).classList.toggle('hidden');
        }
    </script>

@endsection