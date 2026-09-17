<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $primaryKey = 'id_room';

    protected $fillable = [
        'id_user',
        'nama_room',
        'mode',
        'status',
        'reward',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

        public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }

        public function soals()
    {
        return $this->belongsToMany(
            Soal::class,
            'room_soal',
            'id_room',
            'id_soal'
        );
    }

    public function assessmentResults()
    {
        return $this->hasMany(
            AssessmentResult::class,
            'id_room',
            'id_room'
        );
    }
}
