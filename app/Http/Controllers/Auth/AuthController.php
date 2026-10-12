<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([//untuk validasi form login di blade
            'username' => ['required', 'string'],// required itu untuk kolom email harus di isi pada form login
            'password' => ['required', 'string'],
            'role' => ['required', 'in:guru,admin'],
        ],[
            'username.required' => 'Username masih kosong!',
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
            return redirect()->route('admin.dashboard');
        }
 
        return redirect()->route('guru.dashboard');

    }

    public function register(Request $request)
    {
        // 1. Validasi isi form
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'username.required'  => 'Username masih kosong!',
            'username.unique'    => 'Username sudah dipakai, pilih yang lain.',
            'email.required'     => 'Email masih kosong!',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password masih kosong!',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Ulangi password tidak cocok.',
        ]);

        // 2. Simpan ke database
        User::create([
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'guru',
            'status'   => 'pending',
        ]);
 
        // 3. Kembali ke halaman login dengan pesan sukses
        return redirect()->route('login')
            ->with('success', 'Pendaftaran berhasil! Tunggu verifikasi dari admin sebelum login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect()->route('login');
    }
}
