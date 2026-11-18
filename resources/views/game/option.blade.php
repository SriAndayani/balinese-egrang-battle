<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ ucfirst($mode) }} - Malajah Basa Bali</title>
    <link rel="stylesheet" href="{{ asset('css/rooms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/game-base.css') }}">
</head>
<body class="room-body">
   {{-- Latar belakang (memenuhi seluruh layar). Rumput sudah menjadi bagian dari gambar latar.
         Untuk layar besar, lihat contoh srcset di beranda.blade.php. --}}
    <img class="latar" src="{{ asset('images/background.webp') }}" alt=""
         decoding="async" fetchpriority="high" draggable="false">

    {{-- Panggung: semua elemen ditata di dalam kotak ini --}}
    <main class="panggung">

        {{-- Papan judul.
             Tulisan "Pilih Ruangan" digambar oleh CSS di atas papan polos.
             Kalau title-board.webp SUDAH berisi tulisannya, hapus baris <span> di bawah. --}}
        <h1 class="papan-judul">
            <img src="{{ asset('images/title-board.webp') }}" alt="" decoding="async" draggable="false">
            <span>Pilih Ruangan</span>
        </h1>

        {{-- Panah sebelumnya --}}
        <button type="button" class="panah kiri" id="prev" aria-label="Room sebelumnya">
            <img src="{{ asset('images/left-btn.webp') }}" alt="" draggable="false">
        </button>

        {{-- Daftar room. Jumlah per halaman diatur CSS (--per-halaman di rooms.css).
             id="room-list" dan struktur kartunya dipakai rooms.js, jadi jangan diubah. --}}
        <div class="room-list" id="room-list">
            @forelse ($rooms as $room)
                {{-- Satu kartu = satu tautan. Tujuannya sudah ditentukan controller lewat $rute --}}
                <a class="room-card" href="{{ route($rute, $room->id_room) }}">
                    <img src="{{ asset('images/option-board.webp') }}" alt="" decoding="async" draggable="false">
                    <span class="room-label">
                        <b>{{ $room->nama_room }}</b>
                    </span>
                </a>
            @empty
                <div class="room-card kosong">
                    <img src="{{ asset('images/option-board.webp') }}" alt="" decoding="async" draggable="false">
                    <span class="room-label">
                        <b>Belum ada room</b>
                        <small>Tunggu guru membuka room</small>
                    </span>
                </div>
            @endforelse
        </div>

        {{-- Panah berikutnya --}}
        <button type="button" class="panah kanan" id="next" aria-label="Room berikutnya">
            <img src="{{ asset('images/right-btn.webp') }}" alt="" draggable="false">
        </button>
    </main>

    {{-- Tombol kembali ke beranda (pojok kiri bawah layar). Hapus blok ini kalau tidak diinginkan. --}}
    <a class="btn-home" href="{{ url('/') }}" aria-label="Kembali ke beranda">
        <img src="{{ asset('images/home-btn.webp') }}" alt="" decoding="async" draggable="false">
    </a>
 
    {{-- JavaScript halaman room --}}
    <script src="{{ asset('js/rooms.js') }}"></script>
</body>
</html>