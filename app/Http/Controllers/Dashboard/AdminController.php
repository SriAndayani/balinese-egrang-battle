<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Soal;
use App\Models\Room;


class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function dashboardAdmin()
    {
        // Total soal
        $totalSoal = Soal::count();

        // Total guru aktif
        $totalGuruAktif = User::where('role', 'guru')
            ->where('status', 'active')
            ->count();

        // Total guru pending
        $totalGuruPending = User::where('role', 'guru')
            ->where('status', 'pending')
            ->count();

        return view('admin.dashboardAdmin', [
            'roomAktif'       => Room::where('status', true)->count(),
            'totalSoal'       => $totalSoal,
            'totalGuruAktif'  => $totalGuruAktif,
            'totalGuruPending' => $totalGuruPending,
        ]);
    }
}
