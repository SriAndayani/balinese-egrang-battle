<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class GameController extends Controller
{
    public function index(){
        // mengambil seluruh room dengan status 'true'
        $rooms = Room::where('status', true)->get();

        return view('game.index', compact('rooms'));
    }

    public function room(string $mode)
    {
        // Satu tempat untuk semua pengaturan per mode
        $pengaturan = [
            'assesment' => ['label' => 'Kuis',   'rute' => 'assesment.show', 'db' => 'assessment'],
            'battle'    => ['label' => 'Battle', 'rute' => 'battle.show', 'db' => 'battle'],
        ];

        $label = $pengaturan[$mode]['label'];   // untuk judul halaman
        $rute  = $pengaturan[$mode]['rute'];    // route tujuan tombol

        $rooms = Room::where('mode', $pengaturan[$mode]['db'])     // isi kolom mode di database harus sama persis
            ->where('status', 1)
            ->orderBy('id_room')
            ->get();

        return view('game.option', compact('mode', 'label', 'rute', 'rooms'));
    }
}
