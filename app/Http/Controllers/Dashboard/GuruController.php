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
        $idGuru = Auth::id();

        return view('guru.dashboard', [
            'totalRoom' => Room::where('id_user', $idGuru)->count(),

            'roomAktif' => Room::where('id_user', $idGuru)
                               ->where('status', true) 
                               ->count(),
 
            'username'  => Auth::user()->username,
        ]);
    }

    public function dashboardGuru()
    {
        // Hanya room milik guru yang sedang login
        $roomGuru = Room::where('id_user', Auth::id());

        return view('guru.dashboardGuru', [
            'totalRoom' => (clone $roomGuru)->count(),
            'roomAktif' => (clone $roomGuru)->where('status', true)->count(),
        ]);
    }
}
