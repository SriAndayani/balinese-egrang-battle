<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Malajah Basa Bali</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 p-6">

    <div class="max-w-4xl mx-auto">

        {{-- HEADER --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                Game Bahasa Bali
            </h1>
            <p class="text-gray-500 mt-2">
                Pilih room yang tersedia untuk mulai bermain
            </p>
        </div>

        {{--
            Menampilkan pesan error jika ada.

            with('error', '...') di Controller akan tersimpan
            di session flash. Laravel menyediakan helper
            session('key') untuk membacanya di Blade.

            Session flash hanya tersedia untuk satu request berikutnya,
            setelah itu otomatis hilang.
        --}}
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700
                        px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        {{--
            Mengecek apakah $rooms kosong atau tidak.

            $rooms adalah Collection yang dikirim dari GameController.
            @forelse adalah kombinasi @foreach dan @empty:
            → Jika $rooms tidak kosong, jalankan blok pertama
            → Jika $rooms kosong, jalankan blok @empty
        --}}
        @forelse ($rooms as $room)

            {{--
                Setiap room ditampilkan sebagai kartu.
                Kita cek $room->mode untuk menentukan:
                → Warna badge mode
                → Tujuan link ketika kartu diklik
            --}}
            <div class="bg-white rounded-xl shadow p-5 mb-4
                        flex items-center justify-between">

                <div>
                    {{-- Nama room --}}
                    <h2 class="text-lg font-semibold text-gray-800">
                        {{ $room->nama_room }}
                    </h2>

                    {{--
                        Badge mode room.

                        Kita gunakan @if untuk membedakan
                        warna badge berdasarkan mode:
                        → assessment = warna hijau
                        → battle     = warna biru
                    --}}
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs
                                font-medium
                                @if($room->mode === 'assesment')
                                    bg-green-100 text-green-700
                                @else
                                    bg-blue-100 text-blue-700
                                @endif">
                        {{ ucfirst($room->mode) }}
                    </span>
                </div>

                {{--
                    Tombol Mulai.

                    Tujuan link berbeda berdasarkan mode room:
                    → assesment → route('assesment.show', id_room)
                    → battle     → route('battle.show', id_room)
                                   (nanti ditambahkan)

                    route() adalah helper Laravel untuk generate URL
                    berdasarkan nama route yang sudah didefinisikan
                    di web.php.
                --}}
                @if ($room->mode === 'assessment')
                    <a href="{{ route('assesment.show', $room->id_room) }}"
                       class="bg-green-500 hover:bg-green-600 text-white
                              px-5 py-2 rounded-lg text-sm font-medium
                              transition">
                        Mulai
                    </a>
                @else
                    {{--
                        Battle mode belum dibuat.
                        Tombol dibuat disabled agar tidak bisa diklik.
                    --}}
                    <a href="{{ route('battle.show', $room->id_room) }}"
                       class="bg-green-500 hover:bg-green-600 text-white
                              px-5 py-2 rounded-lg text-sm font-medium
                              transition">
                        Mulai
                    </a>
                @endif

            </div>

        @empty

            {{-- Ditampilkan jika tidak ada room aktif sama sekali --}}
            <div class="text-center text-gray-400 py-20">
                <p class="text-lg">Belum ada room yang tersedia.</p>
                <p class="text-sm mt-1">
                    Hubungi guru untuk membuat room baru.
                </p>
            </div>

        @endforelse

    </div>

    <a href="{{ route('login') }}"
    title="Login"
    aria-label="Ke halaman login"
    class="fixed bottom-6 right-6 z-50
            flex items-center justify-center w-14 h-14
            rounded-2xl border border-white/60
            bg-gray-200/60 backdrop-blur-md shadow-lg
            hover:bg-white-200/80 hover:scale-105 active:scale-95
            transition">

        {{-- Ikon rumah (SVG), warnanya mengikuti text-blue-500 --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-500"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 10.5 12 3l9 7.5"/>
            <path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/>
        </svg>
    </a>
</body>
</html>