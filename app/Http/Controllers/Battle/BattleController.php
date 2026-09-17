<?php

namespace App\Http\Controllers\Battle;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Jawaban;
use Illuminate\Http\Request;

class BattleController extends Controller
{

    // Target progress untuk menang
    const WIN_TARGET = 7;

    // Jumlah jawaban benar berturut-turut untuk speed boost
    const STREAK_BOOST_THRESHOLD = 3;

    // Progress tambahan saat speed boost aktif
    const BOOST_PROGRESS = 2;

    // Progress normal per jawaban benar
    const NORMAL_PROGRESS = 1;

    // Jumlah jawaban salah berturut-turut untuk fall
    const STREAK_FALL_THRESHOLD = 3;

    public function show($room)
    {
        $room = Room::findOrFail($room);

        // Pastikan room memang mode battle dan aktif
        if ($room->mode !== 'battle' || !$room->status) {
            abort(403, 'Room tidak tersedia.');
        }

        return view('battle.show', compact('room'));
    }

    public function start(Request $request, $room)
    {
        $room = Room::findOrFail($room);

        // Validasi nama kedua pemain
        $request->validate([
            'nama_a' => 'required|string|max:255',
            'nama_b' => 'required|string|max:255',
        ]);

        $soalIds = $room->soals()->pluck('soals.id_soal')->toArray();

        // Validasi: pastikan room memiliki soal
        if (empty($soalIds)) {
            return redirect()->back()
                ->with('error', 'Room ini belum memiliki soal.');
        }

        $queueA = $soalIds;
        $queueB = $soalIds;
        shuffle($queueA);
        shuffle($queueB);

        // Inisialisasi seluruh state battle ke session
        session([
            'battle_room_id' => $room->id_room,
            'battle_started' => true,

            // State lengkap Player A
            'battle_player_a' => [
                'nama'         => $request->nama_a,
                'queue'        => $queueA,
                'progress'     => 0,
                'streak_benar' => 0,
                'streak_salah' => 0,
                'is_fallen'    => false,
                'is_finished'  => false,
            ],

            // State lengkap Player B
            'battle_player_b' => [
                'nama'         => $request->nama_b,
                'queue'        => $queueB,
                'progress'     => 0,
                'streak_benar' => 0,
                'streak_salah' => 0,
                'is_fallen'    => false,
                'is_finished'  => false,
            ],

            // Winner dikosongkan dulu
            'battle_winner' => null,
        ]);

        return redirect()->route('battle.play',
            ['room' => $room->id_room]);
    }

    public function play($room)
    {
        $room    = Room::findOrFail($room);
        $playerA = session('battle_player_a');
        $playerB = session('battle_player_b');

        if (!session('battle_started') || !$playerA || !$playerB) {
            return redirect()->route('battle.show',
                ['room' => $room->id_room])
                ->with('error', 'Silakan mulai battle dari awal.');
        }

        if (session('battle_winner')) {
            return redirect()->route('battle.result',
                ['room' => $room->id_room]);
        }

        $soalIdA = $playerA['queue'][0] ?? null;
        $soalIdB = $playerB['queue'][0] ?? null;

        $soalA = $soalIdA
            ? \App\Models\Soal::with('jawabans')->find($soalIdA)
            : null;

        $soalB = $soalIdB
            ? \App\Models\Soal::with('jawabans')->find($soalIdB)
            : null;

        // ✅ Siapkan data soal sebagai array bersih di Controller
        // Jangan lakukan transformasi ini di dalam Blade
        $soalAData = $soalA ? [
            'id_soal'    => $soalA->id_soal,
            'pertanyaan' => $soalA->pertanyaan,
            'gambar'     => $soalA->gambar,
            'jawabans'   => $soalA->jawabans->map(fn($j) => [
                'id_jawaban'   => $j->id_jawaban,
                'teks_jawaban' => $j->teks_jawaban,
                'gambar'       => $j->gambar,
            ])->values()->toArray(),
        ] : null;

        $soalBData = $soalB ? [
            'id_soal'    => $soalB->id_soal,
            'pertanyaan' => $soalB->pertanyaan,
            'gambar'     => $soalB->gambar,
            'jawabans'   => $soalB->jawabans->map(fn($j) => [
                'id_jawaban'   => $j->id_jawaban,
                'teks_jawaban' => $j->teks_jawaban,
                'gambar'       => $j->gambar,
            ])->values()->toArray(),
        ] : null;

        return view('battle.play', compact(
            'room',
            'playerA',
            'playerB',
            'soalAData',
            'soalBData'
        ));
    }

    public function answer(Request $request, $room)
    {
        $room = Room::findOrFail($room);

        $request->validate([
            // player harus 'a' atau 'b'
            'player'     => 'required|in:a,b',
            'id_jawaban' => 'required|integer',
        ]);

        $player = $request->player; // 'a' atau 'b'

        // Ambil state pemain yang sedang menjawab dari session
        // 'battle_player_a' atau 'battle_player_b'
        $sessionKey = 'battle_player_' . $player;
        $state      = session($sessionKey);

        // Validasi: pastikan state ada dan pemain belum selesai
        if (!$state) {
            return response()->json(['error' => 'State tidak ditemukan.'], 400);
        }

        if ($state['is_finished']) {
            return response()->json(['error' => 'Pemain sudah selesai.'], 400);
        }

        // Ambil data jawaban dari database
        $jawaban = Jawaban::findOrFail($request->id_jawaban);

        // Ambil jawaban yang benar untuk soal ini
        // (untuk dikirim ke frontend agar bisa di-highlight)
        $jawabanBenar = Jawaban::where('id_soal', $jawaban->id_soal)
            ->where('is_correct', true)
            ->first();

        // Proses jawaban menggunakan helper method
        if ($jawaban->is_correct) {
            $state = $this->handleBenar($state);
        } else {
            $state = $this->handleSalah($state, $jawaban->id_soal);
        }

        // Cek win condition setelah progress diupdate
        $winner = null;
        if ($state['progress'] >= self::WIN_TARGET) {
            $state['is_finished'] = true;
            $state['progress']    = self::WIN_TARGET; // cap progress di 7

            // Simpan pemenang ke session
            session(['battle_winner' => $player]);
            $winner = $player;
        }

        // Simpan state yang sudah diupdate kembali ke session
        session([$sessionKey => $state]);

        // Ambil soal berikutnya dari queue
        // (queue sudah diupdate di dalam handleBenar/handleSalah)
        $nextSoalId = $state['queue'][0] ?? null;
        $nextSoal   = null;

        if ($nextSoalId && !$state['is_finished']) {
            $nextSoal = \App\Models\Soal::with('jawabans')
                ->find($nextSoalId);
        }

        // Kembalikan response JSON ke JavaScript
        return response()->json([
            'is_correct'       => $jawaban->is_correct,

            // ID jawaban yang benar (untuk highlight di UI)
            'correct_answer_id' => $jawabanBenar?->id_jawaban,

            // State terbaru pemain
            'progress'         => $state['progress'],
            'streak_benar'     => $state['streak_benar'],
            'streak_salah'     => $state['streak_salah'],
            'is_fallen'        => $state['is_fallen'],
            'is_finished'      => $state['is_finished'],

            // Apakah jawaban ini memicu speed boost
            'is_boost'         => $state['is_boost'] ?? false,

            // Soal berikutnya untuk pemain ini
            'next_soal'        => $nextSoal ? [
                'id_soal'    => $nextSoal->id_soal,
                'pertanyaan' => $nextSoal->pertanyaan,
                'gambar'     => $nextSoal->gambar,
                'jawabans'   => $nextSoal->jawabans->map(fn($j) => [
                    'id_jawaban'   => $j->id_jawaban,
                    'teks_jawaban' => $j->teks_jawaban,
                    'gambar'       => $j->gambar,
                ]),
            ] : null,

            // Pemenang jika ada
            'winner'           => $winner,
            'winner_nama'      => $winner
                ? session('battle_player_' . $winner)['nama']
                : null,
        ]);
    }

    public function result($room)
    {
        $room    = Room::findOrFail($room);
        $winner  = session('battle_winner');
        $playerA = session('battle_player_a');
        $playerB = session('battle_player_b');

        // Jika tidak ada pemenang di session, redirect ke awal
        if (!$winner || !$playerA || !$playerB) {
            return redirect()->route('battle.show',
                ['room' => $room->id_room]);
        }

        // Tentukan data pemenang dan yang kalah
        $winnerData = $winner === 'a' ? $playerA : $playerB;
        $loserData  = $winner === 'a' ? $playerB : $playerA;

        // Bersihkan seluruh session battle setelah hasil ditampilkan
        session()->forget([
            'battle_room_id',
            'battle_started',
            'battle_player_a',
            'battle_player_b',
            'battle_winner',
        ]);

        return view('battle.result', compact(
            'room',
            'winnerData',
            'loserData'
        ));
    }

    private function handleBenar(array $state): array
    {
        // Reset streak salah karena pemain menjawab benar
        $state['streak_salah'] = 0;
        $state['is_fallen']    = false;
        // Increment streak benar
        $state['streak_benar']++;

        $state['is_boost'] = false;

        // Cek apakah streak benar mencapai threshold untuk boost
        if ($state['streak_benar'] >= self::STREAK_BOOST_THRESHOLD) {
            // SPEED BOOST: progress +2
            $state['progress']    += self::BOOST_PROGRESS;
            // Reset streak benar setelah boost
            $state['streak_benar'] = 0;
            $state['is_boost']      = true; // menandai boost
        } else {
            // Normal: progress +1
            $state['progress'] += self::NORMAL_PROGRESS;
        }

        // untuk menghilangkan soal dari antrian karena sudah di jawab benar
        array_shift($state['queue']);

        return $state;
    }

    private function handleSalah(array $state, int $idSoal): array
    {
        // Reset streak benar karena pemain menjawab salah
        $state['streak_benar'] = 0;

        // Increment streak salah
        $state['streak_salah']++;

        // Cek apakah pemain fall (3 salah berturut-turut)
        if ($state['streak_salah'] >= self::STREAK_FALL_THRESHOLD) {
            $state['is_fallen']    = true;
            // Reset streak salah setelah fall
            $state['streak_salah'] = 0;
        } else {
            $state['is_fallen'] = false;
        }

        $soalDikeluarkan = array_shift($state['queue']);
        array_push($state['queue'], $soalDikeluarkan);

        return $state;
    }
}
