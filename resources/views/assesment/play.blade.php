<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soal {{ $nomorSoal }} - {{ $room->nama_room }}</title>
    <script src="https://cdn.tailwindcss.com"></script>

    {{--
        Meta tag CSRF untuk digunakan oleh JavaScript.

        Ketika JavaScript mengirim AJAX request ke Laravel,
        Laravel tetap memerlukan CSRF token.
        Kita simpan token di meta tag agar mudah diakses JS.

        csrf_token() adalah helper Laravel yang mengembalikan
        string token CSRF saat ini.
    --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-6">

    <div class="bg-white rounded-xl shadow p-8 w-full max-w-xl">

        {{-- HEADER: Progress dan info soal --}}
        <div class="mb-6">

            {{-- Nama siswa dan room --}}
            <p class="text-sm text-gray-400 mb-3">
                {{ $nama }} &mdash; {{ $room->nama_room }}
            </p>

            {{-- Progress bar --}}
            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                <div class="bg-green-500 h-2 rounded-full transition-all"
                     {{--
                         Menghitung lebar progress bar dalam persen.
                         Contoh: soal ke-3 dari 10 → (3/10) × 100 = 30%
                         style harus menggunakan inline karena nilai dinamis.
                         Tailwind tidak bisa generate class dinamis seperti
                         w-[30%] dari variabel PHP.
                     --}}
                     style="width: {{ ($nomorSoal / $totalSoal) * 100 }}%">
                </div>
            </div>

            <p class="text-xs text-gray-400 text-right">
                Soal {{ $nomorSoal }} dari {{ $totalSoal }}
            </p>
        </div>

        {{-- PERTANYAAN --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-800 leading-relaxed">
                {{ $soal->pertanyaan }}
            </h2>

            {{--
                Menampilkan gambar soal jika ada.

                $soal->gambar berisi path file gambar
                atau null jika tidak ada gambar.

                asset() adalah helper Laravel untuk generate URL
                ke folder public/.
                Contoh: asset('storage/soal/gambar.jpg')
                → http://localhost/storage/soal/gambar.jpg
            --}}
            @if ($soal->gambar)
                <img src="{{ asset('storage/' . $soal->gambar) }}"
                     alt="Gambar soal"
                     class="mt-4 rounded-lg max-h-48 object-contain">
            @endif
        </div>

        {{-- PILIHAN JAWABAN --}}
        <div id="jawaban-container" class="space-y-3">

            {{--
                Loop seluruh pilihan jawaban milik soal ini.
                $soal->jawabans sudah di-eager load di Controller
                sehingga tidak ada query tambahan di sini.
            --}}
            @foreach ($soal->jawabans as $jawaban)

                {{--
                    Setiap pilihan jawaban adalah sebuah tombol.

                    data-id="{{ $jawaban->id_jawaban }}"
                    → Custom HTML attribute untuk menyimpan id jawaban.
                      JavaScript akan membaca attribute ini ketika
                      tombol diklik untuk mengirim ke Controller.

                    Kita TIDAK menyimpan is_correct di HTML
                    karena itu akan terlihat oleh siswa
                    jika mereka inspect element.
                    Pengecekan is_correct dilakukan di server (Controller).
                --}}
                <button
                    data-id="{{ $jawaban->id_jawaban }}"
                    onclick="pilihJawaban(this)"
                    class="jawaban-btn w-full text-left px-4 py-3
                           rounded-lg border border-gray-200
                           hover:bg-gray-50 transition text-gray-700
                           text-sm font-medium">

                    {{ $jawaban->teks_jawaban }}

                    {{-- Tampilkan gambar jawaban jika ada --}}
                    @if ($jawaban->gambar)
                        <img src="{{ asset('storage/' . $jawaban->gambar) }}"
                             alt="Gambar jawaban"
                             class="mt-2 rounded max-h-24 object-contain">
                    @endif

                </button>

            @endforeach

        </div>

        {{--
            TOMBOL NEXT.

            Tombol ini awalnya disembunyikan (hidden).
            JavaScript akan menampilkannya setelah siswa
            memilih jawaban dan feedback ditampilkan.
        --}}
        <div id="next-container" class="hidden mt-6">

            {{--
                Form Next menggunakan POST request ke route assessment.next.

                Kenapa POST bukan GET?
                → Kita perlu mengirim data (id_soal dan id_jawaban)
                  ke server untuk disimpan ke session.
                → GET request tidak cocok untuk mengirim data
                  yang mengubah state server.
            --}}
            <form id="next-form"
                  action="{{ route('assesment.next', $room->id_room) }}"
                  method="POST">
                @csrf

                {{--
                    Hidden input untuk menyimpan id soal dan id jawaban.
                    Nilainya akan diisi oleh JavaScript setelah
                    siswa memilih jawaban.
                --}}
                <input type="hidden" name="id_soal"
                       value="{{ $soal->id_soal }}">
                <input type="hidden" id="selected-jawaban-id"
                       name="id_jawaban" value="">

                <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600
                               text-white font-semibold py-2 rounded-lg
                               transition">
                    Soal Berikutnya →
                </button>
            </form>

        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- JAVASCRIPT SECTION                                           --}}
    {{-- ============================================================ --}}
    <script>
    /**
     * Mengambil CSRF token dari meta tag.
     *
     * Token ini WAJIB disertakan di setiap AJAX request POST ke Laravel.
     * Tanpa ini, Laravel akan menolak request dengan 419 Page Expired.
     *
     * querySelector() mencari elemen HTML berdasarkan CSS selector.
     * getAttribute() mengambil nilai attribute dari elemen tersebut.
     */
    const csrfToken = document.querySelector('meta[name="csrf-token"]')
                              .getAttribute('content');

    /**
     * Dipanggil ketika siswa mengklik salah satu tombol jawaban.
     *
     * @param {HTMLElement} element - Tombol yang diklik siswa
     */
    function pilihJawaban(element) {

        // Mengambil id_jawaban dari attribute data-id tombol yang diklik.
        const idJawaban = element.getAttribute('data-id');

        // Menonaktifkan semua tombol jawaban setelah siswa memilih.
        // Ini mencegah siswa mengklik jawaban lain setelah feedback muncul.
        //
        // querySelectorAll() mengembalikan NodeList semua elemen
        // yang cocok dengan selector '.jawaban-btn'.
        // forEach() melakukan iterasi pada setiap elemen.
        document.querySelectorAll('.jawaban-btn').forEach(btn => {
            btn.disabled = true;
            btn.classList.add('cursor-not-allowed', 'opacity-70');
        });

        // Mengirim AJAX request ke route assessment.answer.
        //
        // fetch() adalah Web API bawaan browser untuk HTTP request.
        // Ini tidak membutuhkan library apapun (vanilla JS).
        //
    
        fetch('{{ route("assesment.answer", $room->id_room) }}', {
            method: 'POST',

            headers: {
                // Memberitahu server bahwa kita mengirim data JSON
                'Content-Type': 'application/json',

                // CSRF token wajib ada di setiap POST request
                'X-CSRF-TOKEN': csrfToken,
            },

            // body adalah data yang dikirim ke server.
            // JSON.stringify() mengubah object JavaScript ke string JSON.
            // Server akan menerima ini sebagai $request->id_jawaban
            body: JSON.stringify({
                id_jawaban: idJawaban
            }),
        })

        // .then() dijalankan ketika fetch() berhasil mendapat response.
        // response.json() mengubah response JSON dari server
        // menjadi object JavaScript.
        .then(response => response.json())

        // .then() berikutnya menerima data object yang sudah di-parse.
        // data berisi: { is_correct: true/false, correct_answer_id: int }
        .then(data => {

            // Memberikan feedback visual berdasarkan apakah jawaban benar.
            if (data.is_correct) {

                // Jawaban BENAR: ubah tombol yang diklik menjadi hijau
                element.classList.add(
                    'bg-green-100',
                    'border-green-500',
                    'text-green-700'
                );

            } else {

                // Jawaban SALAH: ubah tombol yang diklik menjadi merah
                element.classList.add(
                    'bg-red-100',
                    'border-red-500',
                    'text-red-700'
                );

                // Highlight jawaban yang benar dengan warna hijau.
                // Kita cari tombol yang data-id-nya cocok dengan
                // correct_answer_id yang dikirim server.
                //
                // Kenapa server yang memberi tahu jawaban benar?
                // → Karena jika kita simpan is_correct di HTML,
                //   siswa bisa melihatnya lewat inspect element.
                const correctBtn = document.querySelector(
                    `.jawaban-btn[data-id="${data.correct_answer_id}"]`
                );

                if (correctBtn) {
                    correctBtn.classList.add(
                        'bg-green-100',
                        'border-green-500',
                        'text-green-700'
                    );
                }
            }

            // Mengisi hidden input dengan id jawaban yang dipilih.
            // Ini yang akan dikirim ke server saat tombol Next ditekan.
            document.getElementById('selected-jawaban-id').value = idJawaban;

            // Menampilkan tombol Next setelah feedback muncul.
            // classList.remove('hidden') menghapus class 'hidden'
            // sehingga elemen menjadi terlihat.
            document.getElementById('next-container')
                    .classList.remove('hidden');
        })

        // .catch() dijalankan jika fetch() gagal (misal: no internet)
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');

            // Re-enable tombol jika terjadi error
            // agar siswa bisa mencoba lagi
            document.querySelectorAll('.jawaban-btn').forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('cursor-not-allowed', 'opacity-70');
            });
        });
    }
    </script>

</body>
</html>