<?php

namespace App\Http\Controllers\Assesment;

use App\Http\Controllers\Controller;
use App\Models\AssesmentResult;
use Illuminate\Http\Request;

class AssesmentResultController extends Controller
{
    public function show($result){
        $result = AssesmentResult::with([
            'room',
            'detailResults.soal',
            'detailResults.jawaban',
        ])->findOrFail($result);
        
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
