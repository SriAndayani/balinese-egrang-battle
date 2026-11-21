<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masukkan Nama - {{ $room->nama_room }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/assesment/assesmentShow.css') }}">
</head>
<body class="game-entry">
 
    {{-- Background --}}
    <img class="game-bg"
         src="{{ asset('images/background-games.webp') }}"
         alt="" aria-hidden="true">
 
    <main class="game-stage">
 
        {{-- Nama room untuk pembaca layar --}}
        <h1 class="sr-only">{{ $room->nama_room }}</h1>
 
        <form action="{{ route('assesment.start', $room->id_room) }}"
              method="POST"
              class="game-form"
              novalidate>
            @csrf
 
            {{-- Papan Inputan --}}
            <section class="board">
                <img class="board__img"
                     src="{{ asset('images/bulletin-board.webp') }}"
                     alt="" aria-hidden="true">
 
                {{-- Input nama --}}
                <div class="field field--nama">
                    <label for="nama" class="field__label">Ketik nama</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        autocomplete="off"
                        value="{{ old('nama') }}"
                        class="field__input @error('nama') is-invalid @enderror"
                        @error('nama') aria-invalid="true" aria-describedby="nama-error" @enderror
                    >
                    @error('nama')
                        <p class="field__error" id="nama-error">{{ $message }}</p>
                    @enderror
                </div>
 
                {{-- Input kode room --}}
                <div class="field field--kode">
                    <label for="kode_room" class="field__label">Kode</label>
                    <input
                        type="text"
                        id="kode_room"
                        name="kode_room"
                        autocomplete="off"
                        maxlength="10"
                        class="field__input @error('kode_room') is-invalid @enderror"
                        @error('kode_room') aria-invalid="true" aria-describedby="kode-error" @enderror
                    >
                    @error('kode_room')
                        <p class="field__error" id="kode-error">{{ $message }}</p>
                    @enderror
                </div>
            </section>
 
            {{-- Tombol Mulai --}}
            <button type="submit" class="btn-mulai">
                <img src="{{ asset('images/start-button.webp') }}" alt="" aria-hidden="true">
            </button>
        </form>
    </main>
 
    {{-- Tombol Home (kiri bawah) --}}
    <a href="{{ route('game.index') }}" class="icon-btn icon-btn--home" aria-label="Kembali ke daftar room">
        <img src="{{ asset('images/home-btn.webp') }}" alt="">
    </a>
 
    {{-- Tombol Full Screen (kanan bawah) --}}
    <button type="button" class="icon-btn icon-btn--fullscreen" id="btn-fullscreen" aria-label="Layar penuh">
        <img src="{{ asset('images/full-screen-btn.webp') }}" alt="">
    </button>
 <script src="{{ asset('js/assesmentShow.js') }}"></script>
</body>
</html>