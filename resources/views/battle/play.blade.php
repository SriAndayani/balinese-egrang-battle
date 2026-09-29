<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Battle — {{ $room->nama_room }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/battlePlay.css') }}">
</head>
<body>

{{-- ============================================================
     DATA DARI BACKEND (PHP → JavaScript)
     Kita encode data PHP ke JSON dan simpan di script tag
     agar JavaScript bisa membacanya tanpa AJAX tambahan
     ============================================================ --}}
<script>
    const BATTLE_CONFIG = {
        answerUrl : '{{ route("battle.answer", $room->id_room) }}',
        resultUrl : '{{ route("battle.result", $room->id_room) }}',
        csrfToken : '{{ csrf_token() }}',
        winTarget : 7,

        playerA: {
            nama        : @json($playerA['nama']),
            progress    : {{ $playerA['progress'] }},
            streakBenar : {{ $playerA['streak_benar'] }},
            streakSalah : {{ $playerA['streak_salah'] }},

            {{-- ✅ Variabel sudah disiapkan di Controller, tinggal encode --}}
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

{{-- ============================================================
     LAYOUT UTAMA BATTLE
     ============================================================ --}}
<div class="battle-layout">

    {{-- ========== BARIS 1: AREA KARAKTER ========== --}}
{{-- ========== BARIS 1: AREA KARAKTER (SATU LINTASAN) ========== --}}
<div class="character-area-unified">

    {{-- Info Player A (kiri) --}}
    <div class="player-info-left" id="side-a">
        <div class="player-name-unified player-a-color" id="name-a">—</div>
        <div class="progress-badge player-a-badge" id="progress-info-a">0 / 7</div>

        {{-- Streak dots Player A --}}
        <div class="streak-dots mt-1">
            <div class="streak-dot" id="streak-a-1"></div>
            <div class="streak-dot" id="streak-a-2"></div>
            <div class="streak-dot" id="streak-a-3"></div>
        </div>

        {{-- Popup notifikasi Player A --}}
        <div class="popup-notify" id="popup-a"></div>
    </div>

    {{-- Area lintasan tengah --}}
    <div class="track-unified-container">

        {{-- Label nama di atas lintasan --}}
        <div class="track-labels">
            <span class="track-label player-a-color" id="track-label-a">—</span>
            <span class="track-label player-b-color" id="track-label-b">—</span>
        </div>

        {{-- Lintasan utama --}}
        <div class="track-unified" id="track-main">

            {{-- Garis lintasan Player A --}}
            <div class="lane lane-a">
                <div class="lane-fill lane-fill-a"
                     id="track-fill-a"
                     style="width: 0%">
                </div>
                {{-- Karakter Player A --}}
                <div class="character char-a"
                     id="char-a"
                     style="left: 0%">
                    🏃‍♂️‍➡️
                </div>
            </div>

            {{-- Garis lintasan Player B --}}
            <div class="lane lane-b">
                <div class="lane-fill lane-fill-b"
                     id="track-fill-b"
                     style="width: 0%">
                </div>
                {{-- Karakter Player B --}}
                <div class="character char-b"
                     id="char-b"
                     style="left: 0%">
                    🏃‍➡️
                </div>
            </div>

        </div>

        {{-- Label START dan FINISH --}}
        <div class="track-endpoints">
            <span>START</span>
            <span>🏁 FINISH</span>
        </div>

    </div>

    {{-- Info Player B (kanan) --}}
    <div class="player-info-right" id="side-b">
        <div class="player-name-unified player-b-color" id="name-b">—</div>
        <div class="progress-badge player-b-badge" id="progress-info-b">0 / 7</div>

        {{-- Streak dots Player B --}}
        <div class="streak-dots mt-1" style="justify-content: flex-end;">
            <div class="streak-dot" id="streak-b-1"></div>
            <div class="streak-dot" id="streak-b-2"></div>
            <div class="streak-dot" id="streak-b-3"></div>
        </div>

        {{-- Popup notifikasi Player B --}}
        <div class="popup-notify" id="popup-b"></div>
    </div>

</div>

    {{-- ========== BARIS 2: AREA SOAL ========== --}}
    {{--
        Karena Player A dan Player B bisa mendapat soal berbeda,
        kita tampilkan dua soal secara side by side di tengah.
        Masing-masing soal hanya relevan untuk pemainnya.
    --}}
    <div class="question-area">
        <div style="display:grid;grid-template-columns:1fr 1fr;width:100%;gap:2rem;">

            {{-- Soal Player A --}}
            <div style="text-align:center;border-right:1px solid rgba(255,255,255,0.1);padding-right:1rem;">
                <div style="font-size:clamp(0.65rem,1.2vw,0.8rem);
                            color:rgba(59,130,246,0.8);
                            font-weight:700;
                            letter-spacing:0.1em;
                            margin-bottom:0.4rem;">
                    SOAL PLAYER A
                </div>
                <div class="question-text" id="question-a" style="font-size:clamp(0.9rem,2vw,1.3rem);">
                    Memuat soal...
                </div>
            </div>

            {{-- Soal Player B --}}
            <div style="text-align:center;padding-left:1rem;">
                <div style="font-size:clamp(0.65rem,1.2vw,0.8rem);
                            color: rgba(244, 114, 182, 0.8);
                            font-weight:700;
                            letter-spacing:0.1em;
                            margin-bottom:0.4rem;">
                    SOAL PLAYER B
                </div>
                <div class="question-text" id="question-b" style="font-size:clamp(0.9rem,2vw,1.3rem);">
                    Memuat soal...
                </div>
            </div>

        </div>
    </div>

    {{-- ========== BARIS 3: AREA JAWABAN ========== --}}
    <div class="answer-area">

        {{-- Jawaban Player A (KIRI) --}}
        <div class="answer-side answer-a" id="answers-a">
            {{-- Diisi oleh JavaScript --}}
        </div>

        {{-- Jawaban Player B (KANAN) --}}
        <div class="answer-side answer-b" id="answers-b">
            {{-- Diisi oleh JavaScript --}}
        </div>

    </div>

</div>

{{-- ============================================================
     WIN OVERLAY
     ============================================================ --}}
<div id="win-overlay">
    <div class="winner-card text-center p-10
                rounded-3xl border-4 max-w-lg w-full mx-4"
         id="win-card"
         style="background: linear-gradient(135deg, #1a1a2e, #16213e);">

        <div class="text-8xl mb-4">🏆</div>

        <h1 class="text-5xl font-black text-yellow-400 mb-2"
            id="win-title">
            MENANG!
        </h1>

        <p class="text-2xl font-bold text-white mb-6" id="win-subtitle">
            —
        </p>

        <div class="grid grid-cols-2 gap-4 mb-8 text-sm"
             id="win-stats">
            {{-- Diisi JavaScript --}}
        </div>

        <a href="{{ route('game.index') }}"
           class="block bg-yellow-400 hover:bg-yellow-300
                  text-gray-900 font-black text-xl py-4
                  rounded-2xl transition">
            🏠 Kembali ke Menu
        </a>

    </div>
</div> 
    <script src="{{ asset('js/battlePlay.js') }}"></script>
</body>
</html>