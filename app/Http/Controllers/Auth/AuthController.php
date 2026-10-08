<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([//untuk validasi form login di blade
            'username' => ['required', 'string'],// required itu untuk kolom email harus di isi pada form login
            'password' => ['required', 'string'],
            'role' => ['required', 'in:guru,admin'],
        ],[
            'email.required' => 'Email masih kosong!',
            'password.required' => 'Password masih kosong!', 
        ]);

        //mencocokkan form dengan table user
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'role'     => $request->role,
        ];

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['username' => 'Username, password, atau peran yang dipilih salah.'])
                ->onlyInput('username', 'role');
        }
 
        $user = Auth::user();
 
        // 4. Guru hanya boleh masuk kalau akunnya sudah diverifikasi admin
        if ($user->role === 'guru' && $user->status !== 'verified') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
 
            return back()
                ->withErrors(['username' => 'Akun kamu belum diverifikasi oleh admin.'])
                ->onlyInput('username', 'role');
        }

        // 5. Login sukses: buat ulang session
        $request->session()->regenerate();
 
        // 6. Arahkan sesuai role
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');//route nya harus di sesuaikan
        }
 
        // Dashboard guru belum dibuat? sementara arahkan ke halaman utama
        return Route::has('guru.dashboard')
            ? redirect()->route('guru.dashboard')//route nya harus di sesuaikan
            : redirect('/');

    }

    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect()->route('login');
    }
}
