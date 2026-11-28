<?php

namespace App\Http\Controllers\Battle;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Jawaban;
use Illuminate\Http\Request;

class BattleController extends Controller
{

    const WIN_TARGET = 7;

    const STREAK_BOOST_THRESHOLD = 3;

    const BOOST_PROGRESS = 2;

    const NORMAL_PROGRESS = 1;

    const STREAK_FALL_THRESHOLD = 3;

    public function show($room)
    {
        $room = Room::findOrFail($room);

        if ($room->mode !== 'battle' || !$room->status) {
            abort(403, 'Room tidak tersedia.');
        }

        return view('battle.show', compact('room'));
    }

    public function start(Request $request, $room)
    {
        $room = Room::findOrFail($room);

        $request->validate([
            'nama_a' => 'required|string|max:255',
            'nama_b' => 'required|string|max:255',
        ]);

        $soalIds = $room->soals()->pluck('soals.id_soal')->toArray();

        if (empty($soalIds)) {
            return redirect()->back()
                ->with('error', 'Room ini belum memiliki soal.');
        }

        $queueA = $soalIds;
        $queueB = $soalIds;
        shuffle($queueA);
        shuffle($queueB);

        session([
            'battle_room_id' => $room->id_room,
            'battle_started' => true,

            'battle_player_a' => [
                'nama'         => $request->nama_a,
                'queue'        => $queueA,
                'progress'     => 0,
                'streak_benar' => 0,
                'streak_salah' => 0,
                'is_fallen'    => false,
                'is_finished'  => false,
            ],

            'battle_player_b' => [
                'nama'         => $request->nama_b,
                'queue'        => $queueB,
                'progress'     => 0,
                'streak_benar' => 0,
                'streak_salah' => 0,
                'is_fallen'    => false,
                'is_finished'  => false,
            ],

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
            'player'     => 'required|in:a,b',
            'id_jawaban' => 'required|integer',
        ]);

        $player = $request->player; 

        $sessionKey = 'battle_player_' . $player;
        $state      = session($sessionKey);

        if (!$state) {
            return response()->json(['error' => 'State tidak ditemukan.'], 400);
        }

        if ($state['is_finished']) {
            return response()->json(['error' => 'Pemain sudah selesai.'], 400);
        }

        $jawaban = Jawaban::findOrFail($request->id_jawaban);

        $jawabanBenar = Jawaban::where('id_soal', $jawaban->id_soal)
            ->where('is_correct', true)
            ->first();

        if ($jawaban->is_correct) {
            $state = $this->handleBenar($state);
        } else {
            $state = $this->handleSalah($state, $jawaban->id_soal);
        }

        $winner = null;
        if ($state['progress'] >= self::WIN_TARGET) {
            $state['is_finished'] = true;
            $state['progress']    = self::WIN_TARGET;

            session(['battle_winner' => $player]);
            $winner = $player;
        }

        session([$sessionKey => $state]);


        $nextSoalId = $state['queue'][0] ?? null;
        $nextSoal   = null;

        if ($nextSoalId && !$state['is_finished']) {
            $nextSoal = \App\Models\Soal::with('jawabans')
                ->find($nextSoalId);
        }


        return response()->json([
            'is_correct'       => $jawaban->is_correct,

            'correct_answer_id' => $jawabanBenar?->id_jawaban,

            'progress'         => $state['progress'],
            'streak_benar'     => $state['streak_benar'],
            'streak_salah'     => $state['streak_salah'],
            'is_fallen'        => $state['is_fallen'],
            'is_finished'      => $state['is_finished'],

            'is_boost'         => $state['is_boost'] ?? false,

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


        if (!$winner || !$playerA || !$playerB) {
            return redirect()->route('battle.show',
                ['room' => $room->id_room]);
        }


        $winnerData = $winner === 'a' ? $playerA : $playerB;
        $loserData  = $winner === 'a' ? $playerB : $playerA;

        session()->forget([
            'battle_room_id',
            'battle_started',
            'battle_player_a',
            'battle_player_b',
            'battle_winner',
        ]);
    }

    private function handleBenar(array $state): array
    {
        $state['streak_salah'] = 0;
        $state['is_fallen']    = false;
        $state['streak_benar']++;
        $state['is_boost'] = false;

        if ($state['streak_benar'] >= self::STREAK_BOOST_THRESHOLD) {
            
            $state['progress']    += self::BOOST_PROGRESS;
            
            $state['streak_benar'] = 0;
            $state['is_boost']      = true; 
        } else {
            $state['progress'] += self::NORMAL_PROGRESS;
        }

        array_shift($state['queue']);

        return $state;
    }

    private function handleSalah(array $state, int $idSoal): array
    {
        $state['streak_benar'] = 0;

        $state['streak_salah']++;

        if ($state['streak_salah'] >= self::STREAK_FALL_THRESHOLD) {
            $state['is_fallen']    = true;

            $state['streak_salah'] = 0;
        } else {
            $state['is_fallen'] = false;
        }

        $soalDikeluarkan = array_shift($state['queue']);
        array_push($state['queue'], $soalDikeluarkan);

        return $state;
    }
}
