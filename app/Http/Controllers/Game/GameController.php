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
}
