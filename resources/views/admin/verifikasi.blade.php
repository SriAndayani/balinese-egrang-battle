@extends('admin.dashboard')

@section('title', 'Verifikasi Guru')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800">Verifikasi Guru</h1>

    {{-- ============ TAB FILTER ============
         Daftar tab dibuat dari array, sama seperti menu sidebar.
         key   : nilai yang dikirim lewat alamat (?status=...)
         label : tulisan di tab
         total : angka di dalam kurung
    --}}
    @php
        $tabs = [
            ['key' => 'semua',    'label' => 'semua',         'total' => $jumlah['semua']],
            ['key' => 'pending',  'label' => 'menunggu',      'total' => $jumlah['pending']],
            ['key' => 'active', 'label' => 'terverifikasi', 'total' => $jumlah['active']],
        ];
    @endphp

    <div class="flex flex-wrap gap-2 mt-4">
        @foreach ($tabs as $tab)
            <a href="{{ route('admin.verifikasi.index', ['status' => $tab['key']]) }}"
               class="px-3 py-1 rounded-full text-sm border transition
                      {{ $filter === $tab['key']
                          ? 'bg-blue-500 text-white border-blue-500'
                          : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-100' }}">
                {{ $tab['label'] }} ({{ $tab['total'] }})
            </a>
        @endforeach
    </div>

    {{-- ============ DAFTAR CARD GURU ============ --}}
    <div class="mt-6 flex flex-col gap-4 max-w-3xl">

        @forelse ($daftarGuru as $guru)
            <div class="bg-white rounded-xl border border-gray-200 p-5">

                {{-- Baris atas: avatar + identitas (kiri), badge status (kanan) --}}
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-gray-100 flex items-center justify-center text-xl">
                            👤
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $guru->username }}</p>
                            <p class="text-sm text-gray-500">{{ $guru->email }}</p>
                            @if ($guru->created_at)
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Mendaftar {{ $guru->created_at->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Badge status --}}
                    @if ($guru->status === 'active')
                        <span class="px-3 py-1 rounded-md text-xs font-medium bg-green-100 text-green-700 border border-green-300">
                            Active
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700 border border-yellow-300">
                            Menunggu
                        </span>
                    @endif
                </div>

                {{-- Tombol aksi: hanya untuk guru yang masih menunggu --}}
                @if ($guru->status !== 'active')
                    <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-300">

                        {{-- Terima --}}
                        <form method="POST" action="{{ route('admin.verifikasi.terima', $guru) }}">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-1.5 rounded-lg text-sm font-medium
                                           bg-green-500 hover:bg-green-600 text-white transition">
                                Terima
                            </button>
                        </form>

                        {{-- Tolak (ada konfirmasi dulu karena data akan dihapus) --}}
                        <form method="POST" action="{{ route('admin.verifikasi.tolak', $guru) }}"
                              onsubmit="return confirm('Tolak dan hapus pendaftaran {{ $guru->username }}?')">
                            @csrf
                            <button type="submit"
                                    class="px-4 py-1.5 rounded-lg text-sm font-medium
                                           bg-red-500 hover:bg-red-600 text-white transition">
                                Tolak
                            </button>
                        </form>
                    </div>
                @endif
            </div>

        @empty
            {{-- Tampil kalau tidak ada guru pada filter ini --}}
            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-500 text-sm">
                Tidak ada guru pada kategori ini.
            </div>
        @endforelse

    </div>

@endsection