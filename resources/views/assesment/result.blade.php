<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis - {{ $result->nama }}</title>
    <link rel="stylesheet" href="{{ asset('css/game-base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/assesment/assesmentResult.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="hasil-body">

    {{-- Latar belakang + lapisan gelap tipis --}}
    <img class="latar" src="{{ asset('images/background.webp') }}" alt=""
         decoding="async" fetchpriority="high" draggable="false">
    <div class="redup" aria-hidden="true"></div>

    <main class="hasil-halaman">

        <div class="kartu-bingkai">

            {{-- SCORE CARD. --}}
            <div id="score-card">
                <article class="kartu-hasil">

                    <div class="piala" aria-hidden="true">&#127942;</div>

                    <h1 class="judul-hasil">Selamat!</h1>
                    <p class="nama-siswa">{{ $result->nama }}</p>
                    <p class="nama-room">{{ $result->room->nama_room }}</p>

                    {{-- Nilai akhir --}}
                    <section class="blok-nilai">
                        <p class="label">Nilai Akhir</p>
                        <p class="angka">{{ $result->skor }}</p>
                        <p class="dari">dari 100</p>
                    </section>

                    {{-- Jawaban benar dan salah berdampingan --}}
                    <section class="statistik">
                        <div class="statistik-benar">
                            <p class="angka">{{ $jumlahBenar }}</p>
                            <p class="label">Jawaban Benar</p>
                        </div>
                        <div class="statistik-salah">
                            <p class="angka">{{ $jumlahSalah }}</p>
                            <p class="label">Jawaban Salah</p>
                        </div>
                    </section>

                    <p class="waktu">
                        Diselesaikan pada:
                        {{ $result->waktu_selesai?->locale('id')->translatedFormat('d F Y, H:i') }}
                    </p>

                </article>
            </div>
        </div>

        <div class="aksi">
            <button type="button" class="tombol-game tombol-hijau" id="btn-unduh"
                    data-nama="{{ $result->nama }}">
                Unduh Kartu Skor
            </button>

            <a class="tombol-game tombol-coklat" href="{{ route('game.index') }}">
                Kembali ke daftar room
            </a>
        </div>

    </main>

    <script src="{{ asset('js/assesmentResult.js') }}"></script>

</body>
</html>