<?php

namespace App\Http\Controllers\Assesment;

use App\Http\Controllers\Controller;
use App\Models\AssesmentResult;
use Illuminate\Http\Request;

class AssesmentResultController extends Controller
{
    public function show($result){
        //  Mengambil data hasil beserta relasi yang dibutuhkan
        $result = AssesmentResult::with([
            'room',
            'detailResults.soal',
            'detailResults.jawaban',
        ])->findOrFail($result);

        // Menghitung jumlah benar dan salah dari detail results.
        $jumlahBenar = $result->detailResults
            ->filter(fn($d) => $d->jawaban->is_correct)
            ->count();

        $jumlahSalah = $result->detailResults->count() - $jumlahBenar;
        return view('assesment.result', compact(
            'result',
            'jumlahBenar',
            'jumlahSalah'
        ));
    }
}
