<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Battle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
        }

        @keyframes winner-pulse {
            0%, 100% { transform: scale(1); }
            50%       { transform: scale(1.03); }
        }

        .winner-badge {
            animation: winner-pulse 1s ease-in-out infinite;
        }

        @keyframes confetti-fall {
            0%   { transform: translateY(-10px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }

        .confetti-piece {
            position: fixed;
            border-radius: 2px;
            animation: confetti-fall linear forwards;
            z-index: 0;
        }
    </style>
</head>
<body class="flex items-center justify-center p-6 min-h-screen">

    <div class="relative z-10 w-full max-w-2xl text-center">

        {{-- Trophy --}}
        <div class="text-9xl mb-6 winner-badge"></div>

        {{-- Nama pemenang --}}
        <h1 class="text-5xl font-black text-yellow-400 mb-2">
            {{ $winnerData['nama'] }}
        </h1>
        <p class="text-2xl text-white font-bold mb-2">MENANG!</p>
        <p class="text-purple-300 mb-10 text-lg">
            {{ $room->nama_room }}
        </p>

        {{-- Statistik kedua pemain --}}
        <div class="grid grid-cols-2 gap-6 mb-10">

            {{-- Pemenang --}}
            <div class="bg-yellow-400 bg-opacity-20 border-2
                        border-yellow-400 rounded-2xl p-6">
                <div class="text-4xl mb-2">🥇</div>
                <p class="text-yellow-300 font-black text-xl mb-1">
                    {{ $winnerData['nama'] }}
                </p>
                <p class="text-white text-3xl font-black">
                    {{ $winnerData['progress'] }} / 7
                </p>
                <p class="text-yellow-400 text-sm mt-1">Progress</p>
            </div>

            {{-- Yang kalah --}}
            <div class="bg-white bg-opacity-5 border-2
                        border-white border-opacity-20
                        rounded-2xl p-6">
                <div class="text-4xl mb-2">🥈</div>
                <p class="text-gray-300 font-black text-xl mb-1">
                    {{ $loserData['nama'] }}
                </p>
                <p class="text-white text-3xl font-black">
                    {{ $loserData['progress'] }} / 7
                </p>
                <p class="text-gray-400 text-sm mt-1">Progress</p>
            </div>

        </div>

        {{-- Tombol aksi --}}
        <div class="flex gap-4 justify-center">
            <a href="{{ route('game.index') }}"
               class="bg-purple-600 hover:bg-purple-500 text-white
                      font-black text-lg px-8 py-4 rounded-2xl transition">
                🏠 Menu Utama
            </a>
            <a href="{{ route('battle.show', $room->id_room) }}"
               class="bg-yellow-400 hover:bg-yellow-300 text-gray-900
                      font-black text-lg px-8 py-4 rounded-2xl transition">
                ⚔️ Main Lagi
            </a>
        </div>

    </div>

    {{-- Confetti otomatis saat halaman terbuka --}}
    <script>
    const colors = [
        '#ff6b6b','#ffd93d','#6bcb77',
        '#4d96ff','#ff922b','#cc5de8','#f8f9fa',
    ];

    for (let i = 0; i < 100; i++) {
        setTimeout(() => {
            const p       = document.createElement('div');
            p.className   = 'confetti-piece';
            p.style.left  = Math.random() * 100 + 'vw';
            p.style.top   = '-20px';
            p.style.background = colors[Math.floor(Math.random() * colors.length)];
            p.style.width  = (Math.random() * 10 + 8) + 'px';
            p.style.height = (Math.random() * 10 + 8) + 'px';
            p.style.animationDuration = (Math.random() * 2 + 2) + 's';
            p.style.animationDelay    = Math.random() * 1 + 's';
            document.body.appendChild(p);
            setTimeout(() => p.remove(), 5000);
        }, i * 40);
    }
    </script>

</body>
</html>