<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssesmentResult extends Model
{

    protected $table = 'assessment_results';
    protected $primaryKey = 'id_ass';

    protected $fillable = [
        'id_room',
        'skor',
        'nama',
        'waktu_selesai',
    ];

    protected $casts = [
        'waktu_selesai' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(
            Room::class,
            'id_room',
            'id_room'
        );
    }

    public function detailResults()
    {
        return $this->hasMany(
            DetailResult::class,
            'id_ass',
            'id_ass'
        );
    }
}
