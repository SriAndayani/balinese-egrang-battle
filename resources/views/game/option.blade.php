<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($mode) }} - Malajah Basa Bali</title>
    <link rel="stylesheet" href="{{ asset('css/rooms.css') }}">
</head>
<body class="room-body">
    {{-- Lapisan belakang: latar gelap + rumput --}}
    <div class="latar" aria-hidden="true"></div>
    <img class="rumput" src="{{ asset('images/grass.png') }}" alt="">
 
    <main class="room-page">
        {{-- Panah sebelumnya --}}
        <button type="button" class="panah kiri" id="prev" aria-label="Room sebelumnya">
            <img src="{{ asset('images/left-btn.webp') }}" alt="" draggable="false">
        </button>
 
        {{-- Daftar room: 3 per halaman (layar lebar), 4 per halaman (HP berdiri) --}}
        <div class="room-list" id="room-list">
            @forelse ($rooms as $room)
                {{-- Satu kartu = satu tautan. Tujuannya sudah ditentukan controller lewat $rute --}}
                <a class="room-card" href="{{ route($rute, $room->id_room) }}">
                    <img src="{{ asset('images/papan-polos.webp') }}" alt="" decoding="async" draggable="false">
                    <span class="room-label">
                        <b>{{ $room->nama_room }}</b>
                        <small>{{ $mode === 'battle' ? '' : '' }}</small>
                    </span>
                </a>
            @empty
                <div class="room-card kosong">
                    <img src="{{ asset('images/papan-polos.webp') }}" alt="" decoding="async" draggable="false">
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
 
    {{-- Papan judul di rumput --}}
    <h1 class="papan-judul">
        <img src="{{ asset('images/papan-kayu.webp') }}" alt="" decoding="async" draggable="false">
        <span>{{ ucfirst($mode) }}</span>
    </h1>

    {{-- Tombol kembali ke halaman pilih mode --}}
    <a class="btn-home" href="{{ url('/') }}" aria-label="Kembali ke beranda">
        <img src="{{ asset('images/home-btn.webp') }}" alt="" decoding="async" draggable="false">
    </a>
 
    {{-- JavaScript halaman room --}}
    <script src="{{ asset('js/rooms.js') }}"></script>
</body>
</html>