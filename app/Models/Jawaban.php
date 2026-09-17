<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $primaryKey = 'id_jawaban';

        protected $fillable = [
        'id_soal',
        'teks_jawaban',
        'gambar',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

        public function soal()
    {
        return $this->belongsTo(
            Soal::class,
            'id_soal',
            'id_soal'
        );
    }

        public function detailResults()
    {
        return $this->hasMany(
            DetailResult::class,
            'id_jawaban',
            'id_jawaban'
        );
    }

}
