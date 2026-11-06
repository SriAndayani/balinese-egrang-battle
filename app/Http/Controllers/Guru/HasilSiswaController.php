<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class HasilSiswaController extends Controller
{
    private const TABEL_HASIL  = 'assessment_results';   // satu baris per siswa
    private const TABEL_DETAIL = 'detail_results';       // satu baris per soal yang dijawab siswa

        public function index()
    {
        $daftarRoom = Room::where('id_user', Auth::id())
            ->where('mode', 'assessment')
            ->withCount('soals')                 // $room->soals_count
            ->latest('id_room')
            ->paginate(10);
 
        // Jumlah siswa per room: [id_room => jumlah]
        $jumlahSiswa = DB::table(self::TABEL_HASIL)
            ->whereIn('id_room', $daftarRoom->pluck('id_room'))
            ->select('id_room', DB::raw('COUNT(*) as total'))
            ->groupBy('id_room')
            ->pluck('total', 'id_room');
 
        return view('guru.hasilSiswa', compact('daftarRoom', 'jumlahSiswa'));
    }

    public function show(Room $room)
    {
        Gate::authorize('kelolaHasil', $room); 
        abort_if($room->mode !== 'assessment', 404);
 
        // hasil siswa yang sudah mengerjakan
        $hasil = DB::table(self::TABEL_HASIL)
            ->where('id_room', $room->id_room)
            ->orderBy('waktu_selesai')
            ->orderBy('id_ass')
            ->get();
 
        // Semua jawaban siswa yang ada di room ini
        $detail = DB::table(self::TABEL_DETAIL . ' as d')
            ->join(self::TABEL_HASIL . ' as h', 'h.id_ass', '=', 'd.id_ass')
            ->leftJoin('soals as s', 's.id_soal', '=', 'd.id_soal')
            ->leftJoin('jawabans as j', 'j.id_jawaban', '=', 'd.id_jawaban')
            ->where('h.id_room', $room->id_room)
            ->select(
                'd.id_detail', 'd.id_ass', 'd.id_soal',
                's.pertanyaan', 's.gambar as gambar_soal',
                'j.teks_jawaban', 'j.gambar as gambar_jawaban', 'j.is_correct'
            )
            ->orderBy('d.id_detail')
            ->get();
 
        // hasil siswa
        $namaSiswa = $hasil->pluck('nama', 'id_ass');
 
        $detail->each(function ($d) use ($namaSiswa) {
            $d->nama   = $namaSiswa[$d->id_ass] ?? '-';
            $d->status = is_null($d->is_correct)
                ? 'kosong'
                : ((int) $d->is_correct === 1 ? 'benar' : 'salah');
        });
 
        // ---------- 3. Hasil per soal ----------
        // Urutan soal mengikuti urutan di tabel room_soal
        $urutanSoal = DB::table('room_soal')
            ->where('id_room', $room->id_room)
            ->orderBy('id_rs')
            ->pluck('id_soal')
            ->flip();                            // [id_soal => posisi]
 
        $nomorSoal = [];                         // [id_soal => nomor soal]
 
        $perSoal = $detail->groupBy('id_soal')
            ->sortBy(fn ($baris, $idSoal) => $urutanSoal[$idSoal] ?? PHP_INT_MAX)
            ->values()
            ->map(function ($baris, $i) use (&$nomorSoal) {
                $pertama = $baris->first();
                $nomorSoal[$pertama->id_soal] = $i + 1;
 
                $benar  = $baris->where('status', 'benar')->count();
                $salah  = $baris->where('status', 'salah')->count();
                $kosong = $baris->where('status', 'kosong')->count();
                $total  = max(1, $benar + $salah + $kosong);
 
                return [
                    'nomor'      => $i + 1,
                    'pertanyaan' => $pertama->pertanyaan ?? '(soal sudah dihapus)',
                    'gambar'     => $pertama->gambar_soal,
                    'benar'      => $benar,
                    'salah'      => $salah,
                    'kosong'     => $kosong,
                    // persentase tinggi batang untuk grafik
                    'pb'         => round($benar / $total * 100, 1),
                    'ps'         => round($salah / $total * 100, 1),
                    'pk'         => round($kosong / $total * 100, 1),
                    'baris'      => $baris,
                ];
            });
 
        // ---------- 4. Hasil per siswa ----------
        $detailPerSiswa = $detail->groupBy('id_ass');
 
        $siswa = $hasil->map(function ($h) use ($detailPerSiswa, $nomorSoal) {
            $baris = ($detailPerSiswa->get($h->id_ass) ?? collect())
                ->each(function ($d) use ($nomorSoal) {
                    $d->nomor = $nomorSoal[$d->id_soal] ?? 0;
                })
                ->sortBy('nomor')
                ->values();
 
            return [
                'nama'  => $h->nama,
                'waktu' => $h->waktu_selesai
                            ? Carbon::parse($h->waktu_selesai)->format('d/m/Y H:i')
                            : '-',
                'skor'  => $h->skor,
                'benar' => $baris->where('status', 'benar')->count(),
                'salah' => $baris->where('status', 'salah')->count(),
                'baris' => $baris,
            ];
        });
 
        // ---------- 5. Ringkasan ----------
        $jumlahSiswa = $hasil->count();
        $rataRata    = $jumlahSiswa ? round($hasil->avg('skor'), 1) : 0;
        $tertinggi   = $hasil->sortByDesc('skor')->first();
 
        // 3 besar (skor sama: yang selesai lebih dulu menang)
        $peringkat = $hasil
            ->sortBy([['skor', 'desc'], ['waktu_selesai', 'asc']])
            ->take(3)
            ->values();
 
        // ---------- 6. Grafik distribusi skor ----------
        $jumlahBin = 8;
        $skorMaks  = (int) ($hasil->max('skor') ?? 0);
        $lebar     = max(1, (int) ceil(($skorMaks + 1) / $jumlahBin));
 
        $grafikSkor = [];
        for ($i = 0; $i < $jumlahBin; $i++) {
            $dari   = $i * $lebar;
            $batas  = $dari + $lebar;           // batas atas (tidak termasuk)
            $grafikSkor[] = [
                'dari'   => $dari,
                'sampai' => $batas - 1,
                'jumlah' => $hasil->filter(fn ($h) => $h->skor >= $dari && $h->skor < $batas)->count(),
            ];
        }
 
        $binTerbanyak = max(1, collect($grafikSkor)->max('jumlah'));
        foreach ($grafikSkor as &$bin) {
            $bin['persen'] = round($bin['jumlah'] / $binTerbanyak * 85, 1);   // maks 85% agar angka muat
        }
        unset($bin);
 
        return view('guru.hasilDetail', compact(
            'room', 'jumlahSiswa', 'rataRata', 'tertinggi',
            'peringkat', 'grafikSkor', 'perSoal', 'siswa'
        ));
    }
 
    /**
     * Hapus SEMUA hasil siswa di satu room. Room dan soalnya tidak ikut terhapus.
     */
    public function destroy(Room $room)
    {
        Gate::authorize('kelolaHasil', $room);
 
        DB::transaction(function () use ($room) {
            $idHasil = DB::table(self::TABEL_HASIL)
                ->where('id_room', $room->id_room)
                ->pluck('id_ass');
 
            // Hapus rincian jawaban dulu, baru hasilnya
            DB::table(self::TABEL_DETAIL)->whereIn('id_ass', $idHasil)->delete();
            DB::table(self::TABEL_HASIL)->where('id_room', $room->id_room)->delete();
        });
 
        return redirect()->route('guru.hasil.index')
            ->with('success', "Semua hasil siswa di room {$room->nama_room} berhasil dihapus.");
    }
}
