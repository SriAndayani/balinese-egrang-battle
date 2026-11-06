<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis - {{ $result->nama }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{--
        html2canvas adalah library JavaScript yang mengubah
        elemen HTML menjadi gambar (canvas).
        Kita gunakan ini untuk fitur Download Score Card.

        Diambil dari CDN sehingga tidak perlu install npm.
    --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center
             justify-center p-6">

    <div class="w-full max-w-md">

        {{--
            SCORE CARD.

            Elemen dengan id="score-card" ini yang akan
            di-capture oleh html2canvas dan diunduh sebagai JPEG.

            Semua konten yang ingin muncul di gambar hasil
            harus berada di dalam div ini.
        --}}
        <div id="score-card"
             class="bg-white rounded-xl shadow p-8 text-center mb-4">

            {{-- Icon/emoji sebagai dekorasi --}}
            <div class="text-5xl mb-4">🎉</div>

            <h1 class="text-2xl font-bold text-gray-800 mb-1">
                Selamat, {{ $result->nama }}!
            </h1>

            <p class="text-gray-500 text-sm mb-6">
                {{ $result->room->nama_room }}
            </p>

            {{-- Skor utama ditampilkan besar di tengah --}}
            <div class="bg-green-50 rounded-xl py-6 mb-6">
                <p class="text-sm text-gray-500 mb-1">Nilai Akhir</p>
                <p class="text-6xl font-bold text-green-600">
                    {{ $result->skor }}
                </p>
                <p class="text-sm text-gray-400 mt-1">dari 100</p>
            </div>

            {{-- Statistik benar dan salah berdampingan --}}
            <div class="grid grid-cols-2 gap-4 mb-4">

                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-green-600">
                        {{ $jumlahBenar }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">Jawaban Benar</p>
                </div>

                <div class="bg-red-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-red-500">
                        {{ $jumlahSalah }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">Jawaban Salah</p>
                </div>

            </div>

            {{-- Waktu selesai --}}
            <p class="text-xs text-gray-400">
                Diselesaikan pada:
                {{--
                    $result->waktu_selesai adalah Carbon instance
                    karena kita sudah set cast 'datetime' di Model.

                    format() adalah method Carbon untuk memformat tanggal.
                    'd M Y, H:i' → contoh: "15 Jun 2025, 14:30"
                --}}
                {{ $result->waktu_selesai?->format('d M Y, H:i') }}
            </p>

        </div>

        {{-- TOMBOL DOWNLOAD --}}
        <button onclick="downloadCard()"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white
                       font-semibold py-3 rounded-xl transition mb-3">
            📥 Download Score Card
        </button>

        {{-- TOMBOL KEMBALI --}}
        <a href="{{ route('game.index') }}"
           class="block text-center text-sm text-gray-400
                  hover:text-gray-600">
            ← Kembali ke daftar room
        </a>

    </div>

    <script>
    function downloadCard() {

        const element = document.getElementById('score-card');
        html2canvas(element, {
            scale: 2,

            useCORS: true,

            backgroundColor: '#ffffff',

        }).then(canvas => {

            const imageData = canvas.toDataURL('image/jpeg', 0.9);

            const link = document.createElement('a');

            link.href = imageData;

            link.download = `hasil-kuis-{{ $result->nama }}.jpg`;

            link.click();
        });
    }
    </script>

</body>
</html>