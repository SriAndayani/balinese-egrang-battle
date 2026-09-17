<?php

namespace App\Http\Controllers\Assesment;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Jawaban;
use App\Models\AssesmentResult;
use App\Models\DetailResult;
use Illuminate\Http\Request;

class AssesmentController extends Controller
{
    // method untuk menampilkan halaman form input nama setelah siswa memilih room.
    public function show($room){
        $room = Room::findOrFail($room);

        if ($room->mode !== 'assessment' || !$room->status) {
            abort(403, 'Room tidak tersedia.');
        }

        return view('assesment.show', compact('room'));
    }

    // method untuk memproses input nama siswa dan menyiapkan sesi permainan.
    public function start(Request $request, $room){
        // Ambil data room dari database
        $room = Room::findOrFail($room);

        // Validasi input nama siswa.
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        // Mengambil semua soal yang terkait dengan room ini
        $soals = $room->soals()->with('jawabans')->get();

        // Mengacak urutan soal.
        $soalArray = $soals->toArray();
        shuffle($soalArray);
        $soalIds = array_column($soalArray, 'id_soal');

        // Menyimpan seluruh data sesi permainan ke dalam session(sementara).
        session([
            'assessment_nama'     => $request->nama,
            'assessment_room_id'  => $room->id_room,
            'assessment_soal_ids' => $soalIds,
            'assessment_index'    => 0,
            'assessment_jawaban'  => [],
        ]);

        // Redirect ke halaman play.
        return redirect()->route('assesment.play', ['room' => $room->id_room]);
    }

    // methon untuk menampilkan soal satu per satu berdasarkan posisi di session.
    public function play($room){
        // Ambil data room
        $room = Room::findOrFail($room);

        // Ambil seluruh data sesi yang dibutuhkan.
        $soalIds = session('assessment_soal_ids');
        $index   = session('assessment_index');
        $nama    = session('assessment_nama');

        // Validasi session
        if (!$soalIds || is_null($index)) {
            return redirect()->route('assesment.show', ['room' => $room->id_room])
                ->with('error', 'Silakan mulai kuis dari awal.');
        }

        // Cek apakah semua soal sudah dijawab.
        if ($index >= count($soalIds)) {
            return redirect()->route('assesment.finish', // noted karena belum ada buat blade untuk finish
                ['room' => $room->id_room]);
        }

        // Ambil id soal yang sedang aktif berdasarkan index saat ini.
        $currentSoalId = $soalIds[$index];
        // Ambil data soal dari database beserta seluruh jawabannya.
        $soal = \App\Models\Soal::with('jawabans')->findOrFail($currentSoalId);
        // Menghitung nomor soal yang ditampilkan kepada siswa.
        $nomorSoal   = $index + 1;
        // Total soal untuk ditampilkan di progress bar.
        $totalSoal   = count($soalIds);

        return view('assesment.play', compact(
            'room',
            'soal',
            'nomorSoal',
            'totalSoal',
            'nama'
        ));
    }

    // method untuk menerima jawaban dari JavaScript dan mengembalikan feedback dalam format JSON.
    public function answer(Request $request, $room){
        // Validasi Jawaban
        $request->validate([
            'id_jawaban' => 'required|integer',
        ]);

        // ambil data jawaban dari database berdasarkan id_jawaban
        $jawaban = Jawaban::findOrFail($request->id_jawaban);
        // Cari jawaban yang benar untuk soal yang sama.
        $correctJawaban = Jawaban::where('id_soal', $jawaban->id_soal)
            ->where('is_correct', true)
            ->first();

        // Mengembalikan response JSON ke JavaScript.
        return response()->json([
            'is_correct'        => $jawaban->is_correct,
            'correct_answer_id' => $correctJawaban?->id_jawaban,
        ]);
    }

    // Method untuk menyimpan jawaban ke session dan melanjutkan ke soal berikutnya.
    public function next(Request $request, $room){
        // Ambil data room
        $room = Room::findOrFail($room);

        $request->validate([
            'id_soal'    => 'required|integer',
            'id_jawaban' => 'required|integer',
        ]);

        // Mengambil array jawaban siswa yang sudah tersimpan di session.
        $jawaban = session('assessment_jawaban', []);
        // Menyimpan jawaban siswa ke array.
        $jawaban[$request->id_soal] = $request->id_jawaban;
        // Ambil index saat ini dari session
        $index = session('assessment_index', 0);
        $soalIds = session('assessment_soal_ids', []);
        // Increment index
        $newIndex = $index + 1;
        // Simpan kembali jawaban yang sudah diperbarui ke session
        session([
            'assessment_jawaban' => $jawaban,
            'assessment_index'   => $index + 1,
        ]);

        // Cek apakah soal sudah habis SETELAH index di-increment
        // Jika index baru sudah sama dengan jumlah soal,
        // berarti semua soal sudah dijawab → langsung finish
        // tanpa perlu redirect ke play lagi
        if ($newIndex >= count($soalIds)) {
            return redirect()->route('assesment.finish',
                ['room' => $room->id_room]);
            // Ini aman karena assessment.finish adalah POST route
            // tapi kita redirect ke sana...
        }

        return redirect()->route('assesment.play', ['room' => $room->id_room]);
    }

    // Method ini untuk menghitung skor, menyimpan ke database, dan redirect ke halaman hasil.
    public function finish($room){
        // Ambil data room
        $room = Room::findOrFail($room);

        // Ambil semua data yang dibutuhkan dari session
        $nama    = session('assessment_nama');
        $soalIds = session('assessment_soal_ids');
        $jawaban = session('assessment_jawaban', []);

        // Validasi: pastikan session masih ada dan lengkap
        if (!$nama || !$soalIds) {
            return redirect()->route('game.index')
                ->with('error', 'Sesi permainan tidak ditemukan.');
        }

        $jumlahBenar = 0;
        $jumlahSalah = 0;

        foreach ($jawaban as $idSoal => $idJawaban) {
            // Ambil data jawaban dari database
            $jawabanData = Jawaban::find($idJawaban);

            if ($jawabanData && $jawabanData->is_correct) {
                $jumlahBenar++;
            } else {
                $jumlahSalah++;
            }
        }

        // Menghitung nilai akhir
        $totalSoal  = count($soalIds);
        $nilaiAkhir = $totalSoal > 0
            ? round(($jumlahBenar / $totalSoal) * 100)
            : 0;

        // Menyimpan hasil ke tabel assessment_results.
        $result = AssesmentResult::create([
            'id_room'       => $room->id_room,
            'nama'          => $nama,
            'skor'          => $nilaiAkhir,
            'waktu_selesai' => now(),
        ]);

        foreach ($jawaban as $idSoal => $idJawaban) {
            DetailResult::create([
                'id_ass'     => $result->id_ass,
                'id_soal'    => $idSoal,
                'id_jawaban' => $idJawaban,
            ]);
        }

        session()->forget([
            'assessment_nama',
            'assessment_room_id',
            'assessment_soal_ids',
            'assessment_index',
            'assessment_jawaban',
        ]);
        // Kembali ke halaman hasil
        return redirect()->route('assesment.result.show',
        ['result' => $result->id_ass]);
        
    }

}
