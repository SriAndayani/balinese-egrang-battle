<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;

class GuruController extends Controller
{
    public function dashboard()
    {
        // return view('guru.dashboard');
        $idGuru = Auth::id();

        return view('guru.dashboard', [
            'totalRoom' => Room::where('id_user', $idGuru)->count(),
 
            // Ganti 'aktif' kalau nilai status_room di databasemu berbeda
            'roomAktif' => Room::where('id_user', $idGuru)
                               ->where('status', true) 
                               ->count(),
 
            'username'  => Auth::user()->username,
        ]);
    }
}
