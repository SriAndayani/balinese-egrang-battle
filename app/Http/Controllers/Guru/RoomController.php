<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Soal;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    const JUMLAH_SOAL= 10;

    public function index()
    {
        $daftarRoom = Room::where('id_user', Auth::id())
            ->withCount('soals')          
            ->latest('id_room')
            ->paginate(10);
 
        return view('guru.room', compact('daftarRoom'));
    }
 
    public function create()
    {
        return view('guru.roomFile', [
            'room'       => null,
            'daftarSoal' => $this->daftarSoalTersedia(),
            'terpilih'   => [],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->aturan(), $this->pesan());
 
        $idSoal = $this->soalTersedia()
            ->whereIn('id_soal', $validated['soal'])
            ->pluck('id_soal');
 
        if ($idSoal->count() !== self::JUMLAH_SOAL) {
            return back()->withInput()
                ->withErrors(['soal' => 'Soal yang dipilih tidak tersedia untukmu. Soal harus berjumlah ' . self::JUMLAH_SOAL . '.']);
        }
 
        DB::transaction(function () use ($request, $validated, $idSoal) {
            $room = Room::create([
                'id_user'   => Auth::id(),
                'nama_room' => $validated['nama_room'],
                'kode_room' => $this->kodeDariForm($validated),
                'mode'      => $validated['mode'],
                'reward'     => $validated['reward'] ?? null,
                'status'    => $request->boolean('status'),
            ]);
 
            $room->soals()->sync($idSoal);   // isi tabel room_soal
        });
 
        return redirect()->route('guru.room.index')
            ->with('success', 'Room berhasil dibuat.');
    }
 
    public function edit(Room $room)
    {
        Gate::authorize('update', $room);   // 403 kalau bukan room miliknya
 
        return view('guru.roomFile', [
            'room'       => $room,
            'daftarSoal' => $this->daftarSoalTersedia(),
            'terpilih'   => $room->soals->pluck('id_soal')->all(),
        ]);
    }
 
    public function update(Request $request, Room $room)
    {
        Gate::authorize('update', $room);
 
        $validated = $request->validate($this->aturan(), $this->pesan());
 
        $idSoal = $this->soalTersedia()
            ->whereIn('id_soal', $validated['soal'])
            ->pluck('id_soal');
 
        if ($idSoal->count() !== self::JUMLAH_SOAL) {
            return back()->withInput()
                ->withErrors(['soal' => 'Soal yang dipilih tidak tersedia untukmu.Soal harus berjumlah ' . self::JUMLAH_SOAL . '.']);
        }
 
        DB::transaction(function () use ($request, $validated, $room, $idSoal) {
            $room->update([
                'nama_room' => $validated['nama_room'],
                'kode_room' => $this->kodeDariForm($validated),
                'mode'      => $validated['mode'],
                'reward'     => $validated['reward'] ?? null,
                'status'    => $request->boolean('status'),
            ]);
 
            $room->soals()->sync($idSoal);   // samakan isi room_soal dengan pilihan baru
        });
 
        return redirect()->route('guru.room.index')
            ->with('success', 'Room berhasil diperbarui.');
    }
 
    public function destroy(Room $room)
    {
        Gate::authorize('delete', $room);
 
        try {
            $room->delete();
        } catch (QueryException $e) {
            // Kode 23000 = room ini masih dirujuk tabel lain (misalnya hasil siswa)
            if ($e->getCode() === '23000') {
                return back()->withErrors([
                    'hapus' => "Room {$room->nama_room} tidak bisa dihapus karena sudah punya hasil siswa. "
                             . 'Nonaktifkan saja lewat halaman Detail.',
                ]);
            }
 
            throw $e;
        }
 
        return back()->with('success', "Room {$room->nama_room} berhasil dihapus.");
    }

    private function soalTersedia()
    {
        $idGuru = Auth::id();
 
        return Soal::where(function ($query) use ($idGuru) {
            $query->where('id_user', $idGuru)
                  ->orWhereHas('pembuat', fn ($pembuat) => $pembuat->where('role', 'admin'));
        });
    }
 
    /** Daftar soal untuk ditampilkan sebagai pilihan di form. */
    private function daftarSoalTersedia()
    {
        return $this->soalTersedia()
            ->with(['kategori', 'pembuat'])
            ->latest('id_soal')
            ->get();
    }

    private function kodeDariForm(array $validated): ?string
    {
        return $validated['mode'] === 'assessment'
            ? strtoupper($validated['kode_room'])
            : null;
    }
 
    private function aturan(): array
    {
        return [
            'nama_room' => ['required', 'string', 'max:255'],
            'mode'      => ['required', Rule::in(['assessment', 'battle'])],
            'kode_room' => ['exclude_unless:mode,assessment', 'required', 'string', 'min:4', 'max:10', 'regex:/^[A-Za-z0-9]+$/'],
            'reward'     => ['nullable', 'integer', 'min:0', 'max:10000'],
            'soal'      => ['required', 'array', 'min:1'],
            'soal.*'    => ['integer'],
        ];
    }
 
    private function pesan(): array
    {
        return [
            'nama_room.required' => 'Nama room masih kosong!',
            'nama_room.max'      => 'Nama room maksimal 255 karakter.',
            'mode.required'      => 'Pilih mode room.',
            'mode.in'            => 'Mode harus assessment atau battle.',
            'kode_room.required' => 'Kode room wajib diisi untuk room assessment.',
            'kode_room.min'      => 'Kode room minimal 4 karakter.',
            'kode_room.max'      => 'Kode room maksimal 10 karakter.',
            'kode_room.regex'    => 'Kode room hanya boleh huruf dan angka, tanpa spasi.',
            'reward.integer'      => 'Poin harus berupa angka bulat.',
            'reward.min'          => 'Poin tidak boleh minus.',
            'reward.max'          => 'Poin maksimal 10000.',
            'soal.required'      => 'Pilih minimal 1 soal untuk room ini.',
            'soal.min'           => 'Pilih minimal 1 soal untuk room ini.',
        ];
    }
}
