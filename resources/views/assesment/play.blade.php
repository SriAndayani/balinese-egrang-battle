<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soal {{ $nomorSoal }} - {{ $room->nama_room }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/assesmentPlay.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="game-soal">
 
    {{-- Background --}}
    <img class="game-bg"
         src="{{ asset('images/background-games.webp') }}"
         alt="" aria-hidden="true">
 
    {{-- Nomor soal (kiri atas) --}}
    <p class="hud">
        Soal {{ $nomorSoal }}<small> / {{ $totalSoal }}</small>
        <span class="sr-only">{{ $nama }} - {{ $room->nama_room }}</span>
    </p>
 
    <main class="quiz">
 
        {{-- PAPAN SOAL --}}
        <section class="board">
            <img class="board__img"
                 src="{{ asset('images/question-board.webp') }}"
                 alt="" aria-hidden="true">
 
            <div class="board__content">
                <h1 class="board__question">{{ $soal->pertanyaan }}</h1>
 
                @if ($soal->gambar)
                    <img class="board__figure"
                         src="{{ asset('storage/' . $soal->gambar) }}"
                         alt="Gambar soal">
                @endif
            </div>
        </section>
 
        {{-- PILIHAN JAWABAN
             data-answer-url dibaca JS (tidak ada lagi script inline).
             is_correct TIDAK disimpan di HTML; dicek di server. --}}
        <div id="jawaban-container"
             class="answers"
             role="group"
             aria-label="Pilihan jawaban"
             data-answer-url="{{ route('assesment.answer', $room->id_room) }}">
 
            @foreach ($soal->jawabans as $jawaban)
                <button type="button"
                        class="answer"
                        data-id="{{ $jawaban->id_jawaban }}">
 
                    <span class="answer__badge" aria-hidden="true">{{ chr(97 + $loop->index) }}</span>
 
                    <span class="answer__body">
                        <span class="answer__text">{{ $jawaban->teks_jawaban }}</span>
                        @if ($jawaban->gambar)
                            <img class="answer__img"
                                 src="{{ asset('storage/' . $jawaban->gambar) }}"
                                 alt="Gambar jawaban">
                        @endif
                    </span>
 
                    <span class="answer__status" aria-hidden="true"></span>
                </button>
            @endforeach
        </div>
 
        {{-- Pengumuman hasil untuk pembaca layar --}}
        <p id="feedback" class="sr-only" aria-live="polite"></p>
    </main>
 
    {{-- Tombol Next: muncul setelah siswa memilih jawaban --}}
    <form id="next-form"
          class="nav-slot nav-slot--next"
          action="{{ route('assesment.next', $room->id_room) }}"
          method="POST"
          hidden>
        @csrf
        <input type="hidden" name="id_soal" value="{{ $soal->id_soal }}">
        <input type="hidden" id="selected-jawaban-id" name="id_jawaban" value="">
 
        <button type="submit" class="nav-btn" id="btn-next" aria-label="Soal berikutnya">
            <img src="{{ asset('images/right-btn.webp') }}" alt="">
        </button>
    </form>
<script src="{{ asset('js/assesmentPlay.js') }}"></script>
</body>
</html>