<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Soal;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SoalController extends Controller
{
    public function index(Request $request)
    {
        $kategoriDipilih = $request->query('kategori');
 
        $daftarSoal = Soal::with(['kategori', 'pembuat'])
            ->when($kategoriDipilih, fn ($query) => $query->where('id_kategori', $kategoriDipilih))
            ->latest('id_soal')
            ->paginate(10)
            ->withQueryString();   // supaya filter tidak hilang saat pindah halaman
 
        $daftarKategori = Kategori::orderBy('nama_kategori')->get();
 
        return view('admin.soal', compact('daftarSoal', 'daftarKategori', 'kategoriDipilih'));
    }

    public function create()
    {
        return view('admin.soalForm', [
            'soal'           => null,
            'daftarKategori' => Kategori::orderBy('nama_kategori')->get(),
            'teks'           => [],     // belum ada jawaban
            'indexBenar'     => null,   // belum ada jawaban benar
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->aturan(), $this->pesan());
 
        // Soal dan jawabannya disimpan bersamaan: gagal satu, gagal semua.
        DB::transaction(function () use ($request, $validated) {
            $soal = Soal::create([
                'id_user'     => Auth::id(),
                'id_kategori' => $validated['id_kategori'],
                'pertanyaan'  => $validated['pertanyaan'],
                'gambar'      => $request->hasFile('gambar')
                                    ? $request->file('gambar')->store('soal', 'public')
                                    : null,
            ]);
 
            $this->simpanJawaban($soal, $validated['jawaban'], (int) $validated['benar']);
        });
 
        return redirect()->route('admin.soal.index')
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(Soal $soal)
    {
        Gate::authorize('update', $soal);   // 403 kalau bukan pembuatnya
 
        $jawabans = $soal->jawabans()->orderBy('id_jawaban')->get();
 
        // Urutan (0-3) jawaban yang benar. search() mengembalikan false kalau tidak ada.
        $indexBenar = $jawabans->search(fn ($jawaban) => $jawaban->is_correct);
 
        return view('admin.soalForm', [
            'soal'           => $soal,
            'daftarKategori' => Kategori::orderBy('nama_kategori')->get(),
            'teks'           => $jawabans->pluck('teks_jawaban')->all(),
            'indexBenar'     => $indexBenar === false ? null : $indexBenar,
        ]);
    }
 
    public function update(Request $request, Soal $soal)
    {
        Gate::authorize('update', $soal);
 
        $validated = $request->validate($this->aturan(), $this->pesan());
 
        // Tentukan gambar: ganti kalau ada upload baru, kosongkan kalau dicentang "hapus"
        $gambarLama = $soal->gambar;
        $gambarBaru = $gambarLama;
 
        if ($request->hasFile('gambar')) {
            $gambarBaru = $request->file('gambar')->store('soal', 'public');
        } elseif ($request->boolean('hapus_gambar')) {
            $gambarBaru = null;
        }
 
        DB::transaction(function () use ($validated, $soal, $gambarBaru) {
            $soal->update([
                'id_kategori' => $validated['id_kategori'],
                'pertanyaan'  => $validated['pertanyaan'],
                'gambar'      => $gambarBaru,
            ]);
 
            $this->simpanJawaban($soal, $validated['jawaban'], (int) $validated['benar']);
        });
 
        // File gambar lama dihapus SETELAH data berhasil tersimpan
        if ($gambarLama && $gambarLama !== $gambarBaru) {
            Storage::disk('public')->delete($gambarLama);
        }
 
        return redirect()->route('admin.soal.index')
            ->with('success', 'Soal berhasil diubah.');
    }

    public function destroy(Soal $soal)
    {
        Gate::authorize('delete', $soal);
 
        try {
            DB::transaction(function () use ($soal) {
                $soal->jawabans()->delete();
                $soal->delete();
            });
        } catch (QueryException $e) {
            // Kode 23000 = soal ini masih dipakai tabel lain (room, riwayat jawaban, dll.)
            if ($e->getCode() === '23000') {
                return back()->withErrors([
                    'hapus' => 'Soal tidak bisa dihapus karena masih dipakai.',
                ]);
            }
 
            throw $e;
        }
 
        if ($soal->gambar) {
            Storage::disk('public')->delete($soal->gambar);
        }
 
        return back()->with('success', 'Soal berhasil dihapus.');
    }

    private function simpanJawaban(Soal $soal, array $teks, int $indexBenar): void
    {
        $lama = $soal->jawabans()->orderBy('id_jawaban')->get();
 
        foreach (array_values($teks) as $i => $isi) {
            $data = [
                'teks_jawaban' => $isi,
                'is_correct'     => ($i === $indexBenar),
            ];
 
            if (isset($lama[$i])) {
                $lama[$i]->update($data);
            } else {
                $soal->jawabans()->create($data);
            }
        }
    }
 
    /** Aturan validasi form soal. */
    private function aturan(): array
    {
        return [
            'pertanyaan'  => ['required', 'string', 'max:255'],
            'id_kategori' => ['required', Rule::exists(Kategori::class, 'id_kategori')],
            'gambar'      => ['nullable', 'image', 'max:2048'],   // maksimal 2 MB
            'jawaban'     => ['required', 'array', 'size:4'],
            'jawaban.*'   => ['required', 'string', 'max:200'],
            'benar'       => ['required', 'integer', 'between:0,3'],
        ];
    }
 
    /** Pesan error */
    private function pesan(): array
    {
        return [
            'pertanyaan.required'  => 'Pertanyaan masih kosong!',
            'pertanyaan.max'       => 'Pertanyaan maksimal 255 karakter.',
            'id_kategori.required' => 'Pilih kategori dulu.',
            'id_kategori.exists'   => 'Kategori tidak valid.',
            'gambar.image'         => 'File harus berupa gambar (jpg, png, dan sebagainya).',
            'gambar.max'           => 'Ukuran gambar maksimal 2 MB.',
            'jawaban.required'     => 'Isi keempat pilihan jawaban.',
            'jawaban.size'         => 'Pilihan jawaban harus berjumlah 4.',
            'jawaban.*.required'   => 'Semua pilihan jawaban harus diisi.',
            'jawaban.*.max'        => 'Setiap jawaban maksimal 200 karakter.',
            'benar.required'       => 'Tandai satu jawaban yang benar.',
            'benar.between'        => 'Jawaban yang benar tidak valid.',
        ];
    }
}
