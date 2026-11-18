<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="X-UA-Compatible" content="ie=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
 <title>Ngiring Melajah Basa Bali - Web</title>
 <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>
<body class="beranda">

<img class="latar" src="{{ asset('images/background.webp') }}" alt=""
        decoding="async" fetchpriority="high" draggable="false">

{{-- ===== Panggung: semua elemen ditata di dalam kotak ini ===== --}}
<main class="panggung">

    {{-- Judul --}}
    <h1 class="judul">
        <img src="{{ asset('images/tittle-board.webp') }}" alt="Ngiring Malajah Basa Bali"
                decoding="async" fetchpriority="high" draggable="false">
    </h1>

    {{-- Karakter (hiasan, tidak perlu dibacakan pembaca layar) --}}
    <img class="karakter karakter-iluh" src="{{ asset('images/iluh-namaste-pose.png') }}" alt=""
            decoding="async" draggable="false">
    <img class="karakter karakter-gede" src="{{ asset('images/gede-namaste-pose.png') }}" alt=""
            decoding="async" draggable="false">

    {{-- Pilihan mode permainan --}}
    <nav class="menu" aria-label="Pilih mode permainan">
        <a class="tombol tombol-uji" href="{{ route('game.room', 'assesment') }}">
            <img src="{{ asset('images/assessment-btn.webp') }}" alt="Uji Kemampuan"
                    decoding="async" draggable="false">
        </a>

        <a class="tombol tombol-egrang" href="{{ route('game.room', 'battle') }}">
            <img src="{{ asset('images/battle-btn.webp') }}" alt="Balap Egrang"
                    decoding="async" draggable="false">
        </a>
    </nav>
</main>

{{-- ===== Tombol login (menempel di pojok layar) ===== --}}
<a class="tombol-login" href="{{ route('login') }}">
    <img src="{{ asset('images/login-btn.webp') }}" alt="Login" decoding="async" draggable="false">
</a>

</body>
</html>