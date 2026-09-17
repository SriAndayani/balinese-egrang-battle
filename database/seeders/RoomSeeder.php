<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assessmentRoom = Room::create([
            'id_user' => 2,
            'nama_room' => 'Latihan 1',
            'mode' => 'assessment',
            'status' => true,
            'reward' => 100
        ]);


        $assessmentRoom->soals()->attach([
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            9,
            10
        ]);

        $battleRoom = Room::create([
            'id_user' => 2,
            'nama_room' => 'Latihan 1',
            'mode' => 'battle',
            'status' => true,
            'reward' => 100
        ]);

        $battleRoom->soals()->attach([
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            9,
            10
        ]); 
    }
}
