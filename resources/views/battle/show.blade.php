<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="only light">
    <title>Battle Mode — {{ $room->nama_room }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/battle/battleShow.css') }}">
</head>
<body class="setup-body">

    {{-- Pesan error dari server (misalnya gagal memulai battle) --}}
    @if (session('error'))
        <div class="pesan-atas" role="alert">{{ session('error') }}</div>
    @endif
 
    <form class="setup" id="form-battle" method="POST" action="{{ route('battle.start', $room->id_room) }}">
        @csrf
 
        {{-- Judul untuk pembaca layar (tidak terlihat) --}}
        <h1 class="sr-only">Battle - {{ $room->nama_room }}</h1>
 
        <div class="papan-baris">
 
            {{-- ===== Pemain 1 ===== --}}
            <section class="pemain">
                <img class="papan" src="{{ asset('images/bulletin-board.webp') }}" alt="" draggable="false">
                <h2 class="judul-papan">Pemain 1</h2>
                <img class="karakter" src="{{ asset('images/gede-half-body.png') }}" alt="" draggable="false">
                <label class="label-nama" for="nama_a">Ketik nama</label>
                <input class="isi-nama" id="nama_a" type="text" name="nama_a"
                       value="{{ old('nama_a') }}" maxlength="20"
                       autocomplete="off" enterkeyhint="next" required>
                @error('nama_a')
                    <p class="pesan-error">{{ $message }}</p>
                @enderror
            </section>
 
            {{-- ===== Pemain 2 ===== --}}
            <section class="pemain">
                <img class="papan" src="{{ asset('images/bulletin-board.webp') }}" alt="" draggable="false">
                <h2 class="judul-papan">Pemain 2</h2>
                <img class="karakter" src="{{ asset('images/iluh-half-body.png') }}" alt="" draggable="false">
                <label class="label-nama" for="nama_b">Ketik nama</label>
                <input class="isi-nama" id="nama_b" type="text" name="nama_b"
                       value="{{ old('nama_b') }}" maxlength="20"
                       autocomplete="off" enterkeyhint="go" required>
                @error('nama_b')
                    <p class="pesan-error">{{ $message }}</p>
                @enderror
            </section>
 
        </div>
 
        {{-- Tombol Mulai --}}
        <button type="submit" class="btn-mulai" id="btn-mulai">
            <img src="{{ asset('images/start-button.webp') }}" alt="" draggable="false">
        </button>
    </form>
 
    {{-- Tombol home (pojok kiri bawah) --}}
    <a class="btn-sudut btn-home" href="{{ route('game.index') }}" aria-label="Kembali ke daftar room">
        <img src="{{ asset('images/home-btn.webp') }}" alt="" draggable="false">
    </a>
 
    {{-- Tombol layar penuh (pojok kanan bawah) --}}
    <button type="button" class="btn-sudut btn-layar" id="btn-layar" aria-label="Layar penuh">
        <img src="{{ asset('images/full-screen-btn.webp') }}" alt="" draggable="false">
    </button>
 
    <script src="{{ asset('js/battleShow.js') }}"></script>
</body>
</html>