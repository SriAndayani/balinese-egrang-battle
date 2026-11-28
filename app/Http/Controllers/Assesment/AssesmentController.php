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
    public function show($room){
        $room = Room::findOrFail($room);

        if ($room->mode !== 'assessment' || !$room->status) {
            abort(403, 'Room tidak tersedia.');
        }

        return view('assesment.show', compact('room'));
    }

    public function start(Request $request, $room){

        $room = Room::findOrFail($room);

        $request->validate([
            'nama' => 'required|string|max:255',
            'kode_room' => 'required|string|max:10',

        ]);

        if (strtoupper(trim($request->kode_room)) !== strtoupper((string) $room->kode_room)) {
            return back()
                ->withErrors(['kode_room' => 'Kode room tidak valid.'])
                ->withInput($request->only('nama'));
        }

        $soals = $room->soals()->with('jawabans')->get();

        $soalArray = $soals->toArray();
        shuffle($soalArray);
        $soalIds = array_column($soalArray, 'id_soal');

        session([
            'assessment_nama'     => $request->nama,
            'assessment_room_id'  => $room->id_room,
            'assessment_soal_ids' => $soalIds,
            'assessment_index'    => 0,
            'assessment_jawaban'  => [],
        ]);

        return redirect()->route('assesment.play', ['room' => $room->id_room]);
    }

    public function play($room){
        $room = Room::findOrFail($room);

        $soalIds = session('assessment_soal_ids');
        $index   = session('assessment_index');
        $nama    = session('assessment_nama');

        if (!$soalIds || is_null($index)) {
            return redirect()->route('assesment.show', ['room' => $room->id_room])
                ->with('error', 'Silakan mulai kuis dari awal.');
        }

        if ($index >= count($soalIds)) {
            return redirect()->route('assesment.finish', 
                ['room' => $room->id_room]);
        }

        $currentSoalId = $soalIds[$index];
        $soal = \App\Models\Soal::with('jawabans')->findOrFail($currentSoalId);
        $nomorSoal   = $index + 1;
        $totalSoal   = count($soalIds);

        return view('assesment.play', compact(
            'room',
            'soal',
            'nomorSoal',
            'totalSoal',
            'nama'
        ));
    }

    public function answer(Request $request, $room){
        $request->validate([
            'id_jawaban' => 'required|integer',
        ]);

        $jawaban = Jawaban::findOrFail($request->id_jawaban);
        $correctJawaban = Jawaban::where('id_soal', $jawaban->id_soal)
            ->where('is_correct', true)
            ->first();

        return response()->json([
            'is_correct'        => $jawaban->is_correct,
            'correct_answer_id' => $correctJawaban?->id_jawaban,
        ]);
    }

    public function next(Request $request, $room){
        $room = Room::findOrFail($room);

        $request->validate([
            'id_soal'    => 'required|integer',
            'id_jawaban' => 'required|integer',
        ]);

        $jawaban = session('assessment_jawaban', []);
        $jawaban[$request->id_soal] = $request->id_jawaban;
        $index = session('assessment_index', 0);
        $soalIds = session('assessment_soal_ids', []);
        $newIndex = $index + 1;
        session([
            'assessment_jawaban' => $jawaban,
            'assessment_index'   => $index + 1,
        ]);

        if ($newIndex >= count($soalIds)) {
            return redirect()->route('assesment.finish',
                ['room' => $room->id_room]);
        }

        return redirect()->route('assesment.play', ['room' => $room->id_room]);
    }

    public function finish($room){

        $room = Room::findOrFail($room);

        $nama    = session('assessment_nama');
        $soalIds = session('assessment_soal_ids');
        $jawaban = session('assessment_jawaban', []);

        if (!$nama || !$soalIds) {
            return redirect()->route('game.index')
                ->with('error', 'Sesi permainan tidak ditemukan.');
        }

        $jumlahBenar = 0;
        $jumlahSalah = 0;

        foreach ($jawaban as $idSoal => $idJawaban) {
            $jawabanData = Jawaban::find($idJawaban);

            if ($jawabanData && $jawabanData->is_correct) {
                $jumlahBenar++;
            } else {
                $jumlahSalah++;
            }
        }

        $totalSoal  = count($soalIds);
        $nilaiAkhir = $totalSoal > 0
            ? round(($jumlahBenar / $totalSoal) * 100)
            : 0;

        $result = AssesmentResult::create([
            'id_room'       => $room->id_room,
            'nama'          => $nama,
            'skor'          => $nilaiAkhir,
            'waktu_selesai' => now(), // noted
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
        return redirect()->route('assesment.result.show',
        ['result' => $result->id_ass]);
        
    }

}
