<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    public function index()
    {
        $daftarKategori = Kategori::orderBy('nama_kategori')->get();

        return view('admin.kategori', compact('daftarKategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:50',
                Rule::unique(Kategori::class, 'nama_kategori'),
            ],
        ], [
            'nama_kategori.required' => 'Nama kategori masih kosong!',
            'nama_kategori.max'      => 'Nama kategori maksimal 50 karakter.',
            'nama_kategori.unique'   => 'Kategori itu sudah ada.',
        ]);
 
        Kategori::create($validated);
 
        return back()->with('success', "Kategori {$validated['nama_kategori']} berhasil ditambahkan.");
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                'max:50',
                Rule::unique(Kategori::class, 'nama_kategori')->ignore($kategori),
            ],
        ], [
            'nama_kategori.required' => 'Nama kategori masih kosong!',
            'nama_kategori.max'      => 'Nama kategori maksimal 50 karakter.',
            'nama_kategori.unique'   => 'Kategori itu sudah ada.',
        ]);
 
        $kategori->update($validated);
 
        return back()->with('success', 'Kategori berhasil diubah.');
    }

    public function destroy(Kategori $kategori)
    {
        try {
            $kategori->delete();
        } catch (QueryException $e) {
            // Kode 23000 = data ini masih dipakai tabel lain (misalnya soal)
            if ($e->getCode() === '23000') {
                return back()->withErrors([
                    'hapus' => "Kategori {$kategori->nama_kategori} tidak bisa dihapus karena masih dipakai.",
                ]);
            }
 
            throw $e;
        }
 
        return back()->with('success', "Kategori {$kategori->nama_kategori} berhasil dihapus.");
    }

}
