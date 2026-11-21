<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis - {{ $result->nama }}</title>
    <link rel="stylesheet" href="{{ asset('css/game-base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/assesmentResult.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{--
        html2canvas adalah library JavaScript yang mengubah
        elemen HTML menjadi gambar (canvas).
        Kita gunakan ini untuk fitur Download Score Card.

        Diambil dari CDN sehingga tidak perlu install npm.
    --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="hasil-body">

    {{-- Latar belakang + lapisan gelap tipis --}}
    <img class="latar" src="{{ asset('images/background.webp') }}" alt=""
         decoding="async" fetchpriority="high" draggable="false">
    <div class="redup" aria-hidden="true"></div>

    <main class="hasil-halaman">

        {{-- Pembungkus luar: hanya untuk animasi muncul dan bayangan (TIDAK ikut diunduh) --}}
        <div class="kartu-bingkai">

            {{--
                SCORE CARD.

                Elemen id="score-card" inilah yang diubah menjadi gambar oleh html2canvas
                (lihat public/js/assessmentResult.js). Semua yang ingin muncul di gambar
                hasil unduhan harus berada di dalam elemen ini.
            --}}
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

                    {{--
                        Waktu selesai. $result->waktu_selesai adalah Carbon karena Model
                        sudah memakai cast 'datetime'.
                        locale('id') + translatedFormat() menghasilkan nama bulan bahasa
                        Indonesia, contoh: "15 Juni 2025, 14:30".
                    --}}
                    <p class="waktu">
                        Diselesaikan pada:
                        {{ $result->waktu_selesai?->locale('id')->translatedFormat('d F Y, H:i') }}
                    </p>

                </article>
            </div>
        </div>

        {{-- Tombol di bawah kartu (di luar #score-card, jadi tidak ikut tergambar) --}}
        <div class="aksi">
            {{-- data-nama dipakai untuk nama file unduhan. Memakai atribut data (bukan menulis
                 nama langsung di JavaScript) supaya nama siswa tidak bisa menyelipkan kode. --}}
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