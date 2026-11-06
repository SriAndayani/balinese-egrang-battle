@extends('guru.dashboard')

@section('title', 'Detail Hasil - ' . $room->nama_room)

@section('content')

    <a href="{{ route('guru.hasil.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        ← Kembali ke daftar
    </a>

    {{-- ============ JUDUL ROOM ============ --}}
    <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $room->nama_room }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                Kode Room:
                <span class="font-mono tracking-wider text-gray-700">{{ $room->kode_room ?? '-' }}</span>
            </p>
        </div>

        @if ($jumlahSiswa > 0)
            <form method="POST" action="{{ route('guru.hasil.destroy', $room) }}"
                  onsubmit="return confirm('Hapus SEMUA hasil siswa di room {{ $room->nama_room }}? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                <button type="submit"
                        class="px-4 py-1.5 rounded-lg text-sm font-medium border border-gray-300
                               text-gray-700 hover:bg-red-50 hover:border-red-300 hover:text-red-600 transition">
                    Hapus semua hasil
                </button>
            </form>
        @endif
    </div>

    {{-- Belum ada siswa yang mengerjakan --}}
    @if ($jumlahSiswa === 0)
        <div class="mt-6 bg-white rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-500 text-sm">
            Belum ada siswa yang mengerjakan room ini.
        </div>
    @else

        {{-- ============================================================
             1. RINGKASAN
             ============================================================ --}}
        <div class="mt-6 bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800">Ringkasan</h2>

            <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Jumlah Siswa</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $jumlahSiswa }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Skor Rata-rata</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $rataRata }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Skor Tertinggi</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">{{ $tertinggi->skor }}</p>
                    <p class="text-sm text-gray-500">{{ $tertinggi->nama }}</p>
                </div>
            </div>

            {{-- Dua grafik berdampingan. Dibuat dari kotak HTML biasa (tanpa library). --}}
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- Grafik 1: benar / salah / tidak dijawab per soal --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-700 text-center">Benar atau salah per soal</h3>

                    <div class="mt-3 flex items-end gap-2 h-40 border-b border-l border-gray-200 px-2">
                        @foreach ($perSoal as $s)
                            <div class="flex-1 h-full flex flex-col-reverse"
                                 title="Soal {{ $s['nomor'] }}: {{ $s['benar'] }} benar, {{ $s['salah'] }} salah, {{ $s['kosong'] }} tidak dijawab">
                                <div class="bg-green-500" style="height: {{ $s['pb'] }}%"></div>
                                <div class="bg-red-400"   style="height: {{ $s['ps'] }}%"></div>
                                <div class="bg-gray-200"  style="height: {{ $s['pk'] }}%"></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex gap-2 px-2 mt-1">
                        @foreach ($perSoal as $s)
                            <div class="flex-1 text-center text-xs text-gray-500">S{{ $s['nomor'] }}</div>
                        @endforeach
                    </div>

                    <div class="mt-3 flex flex-wrap justify-center gap-4 text-xs text-gray-600">
                        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-500 inline-block"></span> Benar</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-400 inline-block"></span> Salah</span>
                        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-gray-200 inline-block"></span> Tidak dijawab</span>
                    </div>
                </div>

                {{-- Grafik 2: sebaran skor --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-700 text-center">Sebaran skor</h3>

                    <div class="mt-3 flex items-end gap-1 h-40 border-b border-l border-gray-200 px-2">
                        @foreach ($grafikSkor as $b)
                            <div class="flex-1 h-full flex flex-col justify-end items-center"
                                 title="Skor {{ $b['dari'] }} - {{ $b['sampai'] }}: {{ $b['jumlah'] }} siswa">
                                @if ($b['jumlah'] > 0)
                                    <span class="text-xs text-gray-600">{{ $b['jumlah'] }}</span>
                                @endif
                                <div class="w-full bg-blue-500" style="height: {{ $b['persen'] }}%"></div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex gap-1 px-2 mt-1">
                        @foreach ($grafikSkor as $b)
                            <div class="flex-1 text-center text-[10px] text-gray-500">{{ $b['dari'] }}</div>
                        @endforeach
                    </div>
                    <p class="mt-2 text-center text-xs text-gray-400">Rentang skor (jumlah siswa di atas batang)</p>
                </div>
            </div>
        </div>

        {{-- ============================================================
             2. PERINGKAT (3 besar)
             ============================================================ --}}
        <div class="mt-6 bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800">Peringkat</h2>

            <div class="mt-4 max-w-xl rounded-lg border border-gray-200 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-blue-500 text-white text-left">
                        <tr>
                            <th class="px-4 py-2 font-medium">Peringkat</th>
                            <th class="px-4 py-2 font-medium">Nama</th>
                            <th class="px-4 py-2 font-medium text-right">Skor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach (['🥇', '🥈', '🥉'] as $i => $medali)
                            @php $p = $peringkat[$i] ?? null; @endphp
                            <tr>
                                <td class="px-4 py-2">{{ $medali }} {{ $i + 1 }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $p->nama ?? '-' }}</td>
                                <td class="px-4 py-2 text-right">{{ $p->skor ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============================================================
             3. HASIL PER SISWA (klik baris untuk membuka rincian)
             <details> adalah elemen HTML bawaan untuk dropdown, tanpa JavaScript.
             ============================================================ --}}
        <div class="mt-6 bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800">Hasil per Siswa</h2>

            <div class="mt-4 rounded-lg border border-gray-200 overflow-hidden">

                {{-- Judul kolom --}}
                <div class="grid grid-cols-12 bg-blue-500 text-white text-sm font-medium px-4 py-2">
                    <div class="col-span-3">Siswa</div>
                    <div class="col-span-3">Dikumpulkan</div>
                    <div class="col-span-2 text-center">Skor</div>
                    <div class="col-span-2 text-center">Benar</div>
                    <div class="col-span-2 text-center">Salah</div>
                </div>

                @foreach ($siswa as $s)
                    <details class="group border-t border-gray-200">
                        <summary class="grid grid-cols-12 items-center px-4 py-3 text-sm cursor-pointer
                                        list-none [&::-webkit-details-marker]:hidden hover:bg-gray-50">
                            <div class="col-span-3 flex items-center gap-2 font-medium text-gray-800">
                                <img
                                    src="{{ asset('icons/chevron-right.svg') }}"
                                    class="w-3 h-3 transition group-open:hidden"
                                    alt=""
                                >
                                <img
                                    src="{{ asset('icons/chevron-down.svg') }}"
                                    class="w-3 h-3 hidden group-open:block transition"
                                    alt=""
                                >
                                {{ $s['nama'] }}
                            </div>
                            <div class="col-span-3 text-gray-600">{{ $s['waktu'] }}</div>
                            <div class="col-span-2 text-center font-medium text-gray-800">{{ $s['skor'] }}</div>
                            <div class="col-span-2 text-center text-green-600">{{ $s['benar'] }}</div>
                            <div class="col-span-2 text-center text-red-500">{{ $s['salah'] }}</div>
                        </summary>

                        {{-- Rincian: soal, jawaban yang dipilih, benar/salah --}}
                        <div class="px-4 pb-4 bg-gray-50">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="py-2 w-10 font-medium">No</th>
                                        <th class="py-2 font-medium">Soal</th>
                                        <th class="py-2 font-medium">Jawaban</th>
                                        <th class="py-2 w-16 text-center font-medium">Hasil</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($s['baris'] as $b)
                                        <tr>
                                            <td class="py-2 text-gray-500">{{ $b->nomor }}</td>
                                            <td class="py-2 pr-3">
                                                <div class="flex items-center gap-2">
                                                    @if ($b->gambar_soal)
                                                        <img src="{{ asset('storage/' . $b->gambar_soal) }}" alt=""
                                                             class="h-10 w-10 object-cover rounded border border-gray-200">
                                                    @endif
                                                    <span class="text-gray-800">
                                                        {{ Str::limit($b->pertanyaan ?? '(soal sudah dihapus)', 70) }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-2 pr-3">
                                                <div class="flex items-center gap-2">
                                                    @if ($b->gambar_jawaban)
                                                        <img src="{{ asset('storage/' . $b->gambar_jawaban) }}" alt=""
                                                             class="h-8 w-8 object-cover rounded border border-gray-200">
                                                    @endif
                                                    <span class="{{ $b->teks_jawaban ? 'text-gray-800' : 'text-gray-400 italic' }}">
                                                        {{ $b->teks_jawaban ?? 'Tidak dijawab' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-2 text-center text-base">
                                                @if ($b->status === 'benar')
                                                    <span class="text-green-600 font-bold">✓</span>
                                                @elseif ($b->status === 'salah')
                                                    <span class="text-red-500 font-bold">✗</span>
                                                @else
                                                    <span class="text-gray-400">–</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endforeach
            </div>
        </div>

        {{-- ============================================================
             4. HASIL PER SOAL (klik baris untuk melihat siswa yang menjawab)
             ============================================================ --}}
        <div class="mt-6 mb-8 bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800">Hasil per Soal</h2>

            <div class="mt-4 rounded-lg border border-gray-200 overflow-hidden">

                {{-- Judul kolom --}}
                <div class="grid grid-cols-12 bg-blue-500 text-white text-sm font-medium px-4 py-2">
                    <div class="col-span-6">Soal</div>
                    <div class="col-span-3 text-center">Benar</div>
                    <div class="col-span-3 text-center">Salah</div>
                </div>

                @foreach ($perSoal as $s)
                    <details class="group border-t border-gray-200">
                        <summary class="grid grid-cols-12 items-center px-4 py-3 text-sm cursor-pointer
                                        list-none [&::-webkit-details-marker]:hidden hover:bg-gray-50">
                        <div class="col-span-6 flex items-center gap-2 text-gray-800">
                            <img
                                src="{{ asset('icons/chevron-right.svg') }}"
                                class="w-3 h-3 transition group-open:hidden"
                                alt=""
                            >
                            <img
                                src="{{ asset('icons/chevron-down.svg') }}"
                                class="w-3 h-3 hidden group-open:block transition"
                                alt=""
                            >
                            <span class="font-medium">{{ $s['nomor'] }}.</span>
                            @if ($s['gambar'])
                                <img
                                    src="{{ asset('storage/' . $s['gambar']) }}"
                                    alt=""
                                    class="h-10 w-10 object-cover rounded border border-gray-200"
                                >
                            @endif
                            <span>
                                {{ Str::limit($s['pertanyaan'], 70) }}

                                @if ($s['kosong'] > 0)
                                    <span class="block text-xs text-gray-400">
                                        {{ $s['kosong'] }} tidak dijawab
                                    </span>
                                @endif
                            </span>
                        </div>
                            <div class="col-span-3 text-center text-green-600">{{ $s['benar'] }}</div>
                            <div class="col-span-3 text-center text-red-500">{{ $s['salah'] }}</div>
                        </summary>

                        {{-- Rincian: siapa saja yang menjawab dan jawabannya --}}
                        <div class="px-4 pb-4 bg-gray-50">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500">
                                        <th class="py-2 font-medium">Siswa</th>
                                        <th class="py-2 font-medium">Jawaban</th>
                                        <th class="py-2 w-16 text-center font-medium">Hasil</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($s['baris'] as $b)
                                        <tr>
                                            <td class="py-2 pr-3 text-gray-800">{{ $b->nama }}</td>
                                            <td class="py-2 pr-3">
                                                <div class="flex items-center gap-2">
                                                    @if ($b->gambar_jawaban)
                                                        <img src="{{ asset('storage/' . $b->gambar_jawaban) }}" alt=""
                                                             class="h-8 w-8 object-cover rounded border border-gray-200">
                                                    @endif
                                                    <span class="{{ $b->teks_jawaban ? 'text-gray-800' : 'text-gray-400 italic' }}">
                                                        {{ $b->teks_jawaban ?? 'Tidak dijawab' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-2 text-center text-base">
                                                @if ($b->status === 'benar')
                                                    <span class="text-green-600 font-bold">✓</span>
                                                @elseif ($b->status === 'salah')
                                                    <span class="text-red-500 font-bold">✗</span>
                                                @else
                                                    <span class="text-gray-400">–</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                @endforeach
            </div>
        </div>

    @endif

@endsection