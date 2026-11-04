<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="X-UA-Compatible" content="ie=edge">
 <title>Document</title>
 <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>
<body>
    <header>
        <h2 class="logo"></h2>
        <nav class="navigation">
            <a href="{{ route('login') }}">Login<span></span></a>
            <a href="{{ route('register') }}">Sign in<span></span></a>
        </nav>
    </header>

    <section class="parallax">
        <img src="{{ asset('images/grass.png') }}" id="grass">

        {{-- Pilihan mode permainan --}}
        <nav class="pilih-mode" aria-label="Pilih mode permainan">
            {{-- Ganti url('/kuis') dan url('/battle') dengan route milikmu, misalnya route('kuis') --}}
            <a class="mode-btn" href="{{ route('game.room', 'assesment') }}">
                <img src="{{ asset('images/papan-kayu.webp') }}" alt="" decoding="async" draggable="false">
                <span class="mode-label">
                    <b>Kuis</b>
                    <small>Main sendiri</small>
                </span>
            </a>

            <a class="mode-btn" href="{{ route('game.room', 'battle') }}">
                <img src="{{ asset('images/papan-kayu.webp') }}" alt="" decoding="async" draggable="false">
                <span class="mode-label">
                    <b>Battle</b>
                    <small>Main berdua</small>
                </span>
            </a>
        </nav>
    </section>
</body>
</html>