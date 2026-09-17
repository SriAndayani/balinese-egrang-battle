<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $primaryKey = 'id_soal';

    protected $fillable = [
        'id_kategori',
        'pertanyaan',
        'gambar',
    ];

    public function kategori()
    {
        return $this->belongsTo(
            Kategori::class,
            'id_kategori',
            'id_kategori'
        );
    }

    public function jawabans()
    {
        return $this->hasMany(
            Jawaban::class,
            'id_soal',
            'id_soal'
        );
    }

        public function rooms()
    {
        return $this->belongsToMany(
            Room::class,
            'room_soal',
            'id_soal',
            'id_room'
        );
    }

        public function detailResults()
    {
        return $this->hasMany(
            DetailResult::class,
            'id_soal',
            'id_soal'
        );
    }
}
