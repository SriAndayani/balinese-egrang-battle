<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="only light">
    <title>Battle - {{ $room->nama_room }}</title>

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/battle/battlePlay.css') }}">
</head>
<body class="battle-body">

<script>
    const BATTLE_CONFIG = {
        answerUrl : '{{ route("battle.answer", $room->id_room) }}',
        csrfToken : '{{ csrf_token() }}',
        winTarget : 7,
        reward    : {{ (int) ($room->reward ?? 0) }},

        gambar: {
            a: {
                berdiri : @json(asset('images/gede-egrang-stand.png')),
                jatuh   : @json(asset('images/gede-fall.png')),
            },
            b: {
                berdiri : @json(asset('images/iluh-egrang-stand.png')),
                jatuh   : @json(asset('images/iluh-fall.png')),
            },
        },

        playerA: {
            nama        : @json($playerA['nama']),
            progress    : {{ $playerA['progress'] }},
            streakBenar : {{ $playerA['streak_benar'] }},
            streakSalah : {{ $playerA['streak_salah'] }},
            soal        : @json($soalAData),
        },

        playerB: {
            nama        : @json($playerB['nama']),
            progress    : {{ $playerB['progress'] }},
            streakBenar : {{ $playerB['streak_benar'] }},
            streakSalah : {{ $playerB['streak_salah'] }},
            soal        : @json($soalBData),
        },
    };
</script>

<div class="battle-layout">
    <section class="baris-arena">

        {{-- Skor Pemain 1 (kiri) --}}
        <div class="info-pemain info-a">
            <div class="popup-notify" id="popup-a"></div>
            <div class="progress-badge badge-a" id="progress-info-a">0/7</div>
            <div class="streak-dots">
                <i class="streak-dot" id="streak-a-1"></i>
                <i class="streak-dot" id="streak-a-2"></i>
                <i class="streak-dot" id="streak-a-3"></i>
            </div>
        </div>

        {{-- Arena --}}
        <div class="arena" id="track-main">
            <img class="lantai" src="{{ asset('images/arena.png') }}" alt="" draggable="false">

            {{-- Karakter Pemain 1  --}}
            <div class="kid kid-a" id="char-a" style="--p:0">
                <div class="hopper" id="hopper-a">
                    <img class="sprite" id="img-a" alt="" draggable="false">
                </div>
                <i class="bayangan"></i>
            </div>

            {{-- Karakter Pemain 2 --}}
            <div class="kid kid-b" id="char-b" style="--p:0">
                <div class="hopper" id="hopper-b">
                    <img class="sprite" id="img-b" alt="" draggable="false">
                </div>
                <i class="bayangan"></i>
            </div>
        </div>

        {{-- Skor Pemain 2 (kanan) --}}
        <div class="info-pemain info-b">
            <div class="popup-notify" id="popup-b"></div>
            <div class="progress-badge badge-b" id="progress-info-b">0/7</div>
            <div class="streak-dots">
                <i class="streak-dot" id="streak-b-1"></i>
                <i class="streak-dot" id="streak-b-2"></i>
                <i class="streak-dot" id="streak-b-3"></i>
            </div>
        </div>
    </section>

    {{-- ========== BAGIAN BAWAH: PAPAN SOAL DAN JAWABAN ========== --}}
    <section class="baris-panel">

        {{-- Panel Pemain 1 --}}
        <div class="panel panel-a">
            <h2 class="panel-judul" id="name-a">Pemain 1</h2>
            <div class="kotak-soal"><p class="question-text" id="question-a">Memuat soal...</p></div>
            <div class="jawaban" id="answers-a"></div>
        </div>

        {{-- Panel Pemain 2 --}}
        <div class="panel panel-b">
            <h2 class="panel-judul" id="name-b">Pemain 2</h2>
            <div class="kotak-soal"><p class="question-text" id="question-b">Memuat soal...</p></div>
            <div class="jawaban" id="answers-b"></div>
        </div>

    </section>
</div>

{{-- Tombol home dan layar penuh  --}}
<a class="btn-sudut btn-home" href="{{ route('game.index') }}" aria-label="Kembali ke menu">
    <img src="{{ asset('images/home-btn.webp') }}" alt="" draggable="false">
</a>
<button type="button" class="btn-sudut btn-layar" id="btn-layar" aria-label="Layar penuh">
    <img src="{{ asset('images/full-screen-btn.webp') }}" alt="" draggable="false">
</button>

{{-- LAYAR PEMENANG --}}
<div id="win-overlay">
    <div class="winner-card" id="win-card">
        <div class="win-piala">&#127942;</div>
        <h1 id="win-title">MENANG!</h1>
        <p class="win-reward" id="win-reward"></p>
        <p id="win-subtitle">-</p>
        <a class="btn-hijau" href="{{ route('game.index') }}">Kembali ke Menu</a>
    </div>
</div>

<script src="{{ asset('js/battlePlay.js') }}"></script>
</body>
</html>