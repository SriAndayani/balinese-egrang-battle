<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailResult extends Model
{
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_ass',
        'id_soal',
        'id_jawaban',
    ];


    public function assessmentResult()
    {
        return $this->belongsTo(
            AssessmentResult::class,
            'id_ass',
            'id_ass'
        );
    }

    public function soal()
    {
        return $this->belongsTo(
            Soal::class,
            'id_soal',
            'id_soal'
        );
    }

    public function jawaban()
    {
        return $this->belongsTo(
            Jawaban::class,
            'id_jawaban',
            'id_jawaban'
        );
    }
}
