<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ ucfirst($mode) }} - Malajah Basa Bali</title>
    <link rel="stylesheet" href="{{ asset('css/option.css') }}">
    <link rel="stylesheet" href="{{ asset('css/game-base.css') }}">
</head>
<body class="room-body">
    <img class="latar" src="{{ asset('images/background.webp') }}" alt=""
         decoding="async" fetchpriority="high" draggable="false">

    <main class="panggung">
        <h1 class="papan-judul">
            <img src="{{ asset('images/title-board.webp') }}" alt="" decoding="async" draggable="false">
            <span>Pilih Ruangan</span>
        </h1>

        <button type="button" class="panah kiri" id="prev" aria-label="Room sebelumnya">
            <img src="{{ asset('images/left-btn.webp') }}" alt="" draggable="false">
        </button>

        <div class="room-list" id="room-list">
            @forelse ($rooms as $room)
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

        <button type="button" class="panah kanan" id="next" aria-label="Room berikutnya">
            <img src="{{ asset('images/right-btn.webp') }}" alt="" draggable="false">
        </button>
    </main>

    <a class="btn-home" href="{{ url('/') }}" aria-label="Kembali ke beranda">
        <img src="{{ asset('images/home-btn.webp') }}" alt="" decoding="async" draggable="false">
    </a>
 
    <script src="{{ asset('js/option.js') }}"></script>
</body>
</html>