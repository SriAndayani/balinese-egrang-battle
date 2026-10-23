<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifikasiGuruController extends Controller
{
    public function index(Request $request)
    {
        // Ambil filter dari alamat. Kalau tidak valid, pakai 'semua'.
        $filter = $request->query('status', 'semua');
        if (! in_array($filter, ['semua', 'pending', 'active'])) {
            $filter = 'semua';
        }
 
        // Angka untuk tab: Semua (n), Menunggu (n), Terverifikasi (n)
        $jumlah = [
            'semua'    => User::where('role', 'guru')->count(),
            'pending'  => User::where('role', 'guru')->where('status', 'pending')->count(),
            'active' => User::where('role', 'guru')->where('status', 'active')->count(),
        ];
 
        // Daftar guru yang akan ditampilkan sebagai card
        $daftarGuru = User::where('role', 'guru')
            ->when($filter !== 'semua', fn ($query) => $query->where('status', $filter))
            ->latest()
            ->get();
 
        return view('admin.verifikasi', compact('daftarGuru', 'jumlah', 'filter'));
    }

    public function terima(User $user)
    {
        abort_if($user->role !== 'guru', 404);
 
        $user->update(['status' => 'active']);
 
        return back()->with('success', "Akun {$user->username} berhasil diverifikasi.");
    }

    public function tolak(User $user)
    {
        abort_if($user->role !== 'guru', 404);
 
        $user->delete();
 
        return back()->with('success', "Pendaftaran {$user->username} ditolak dan dihapus.");
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== 'guru', 404);
 
        $user->delete();
 
        return back()->with('success', "Akun {$user->username} berhasil dihapus.");
    }

}
