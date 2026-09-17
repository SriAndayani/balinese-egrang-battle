<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Soal;

class SoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $soal1 = Soal::create([
            'id_kategori' => 3,
            'pertanyaan' => 'Macan, gajah, miwah bojog tuah beburon sane idup ring...'
        ]);

        $soal1->jawabans()->createMany([
            [
                'teks_jawaban' => 'alase',
                'is_correct' => true
            ],
            [
                'teks_jawaban' => 'tukade',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Tegale',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Carike',
                'is_correct' => false
            ]
        ]);


        $soal2 = Soal::create([
            'id_kategori' => 2,
            'pertanyaan' => 'Ipun nyangkol pianak ring warung Pan Komange sambilang meli kopi. Kruna nyangkol ngranjing kruna alus...'
        ]);

        $soal2->jawabans()->createMany([
            [
                'teks_jawaban' => 'Singgih',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Mider',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Madia',
                'is_correct' => true
            ],
            [
                'teks_jawaban' => 'Sor',
                'is_correct' => false
            ]
        ]);


        $soal3 = Soal::create([
            'id_kategori' => 5,
            'pertanyaan' => 'Putu Budi satata ngraos alus sareng anak sane...'
        ]);

        $soal3->jawabans()->createMany([
            [
                'teks_jawaban' => 'Sampun Kauningin',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Timpal Raket',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Kaluwarga',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Durung Kawuningin',
                'is_correct' => true
            ]
        ]);

        $soal4 = Soal::create([
            'id_kategori' => 5,
            'pertanyaan' => 'Musuhe makejang patut impasin. Musuh len raosnyane...'
        ]);

        $soal4->jawabans()->createMany([
            [
                'teks_jawaban' => 'Matimpal',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Meseh',
                'is_correct' => true
            ],
            [
                'teks_jawaban' => 'Manyama',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Mabraya',
                'is_correct' => true
            ]
        ]);
        
        $soal5 = Soal::create([
            'id_kategori' => 4,
            'pertanyaan' => 'Swadharma dados brahmacari wantah...'
        ]);

        $soal5->jawabans()->createMany([
            [
                'teks_jawaban' => 'Malajah',
                'is_correct' => true
            ],
            [
                'teks_jawaban' => 'maboros',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Mamancing',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Majudi',
                'is_correct' => false
            ]
        ]);

        $soal6 = Soal::create([
            'id_kategori' => 1,
            'pertanyaan' => 'Sasuratan kruna Sukra sane patut tuah...'
        ]);

        $soal6->jawabans()->createMany([
            [
                'teks_jawaban' => 'ᬰᬸᬓ᭄ᬭ ᭞᭞',
                'is_correct' => true
            ],
            [
                'teks_jawaban' => 'ᬰᬸᬓᬭ᭞᭞',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'ᬲᬸᬓ᭄ᬭ ᭞᭞',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'ᬘᬸᬓ᭄ᬭ ᭞᭞',
                'is_correct' => false
            ]
        ]);

        $soal7 = Soal::create([
            'id_kategori' => 1,
            'pertanyaan' => 'Aksara suara swalalita kasurat antuk ᬇ aksara punika mawasta...'
        ]);

        $soal7->jawabans()->createMany([
            [
                'teks_jawaban' => 'Akara',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Ikara',
                'is_correct' => true
            ],
            [
                'teks_jawaban' => 'Ukara',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Okara',
                'is_correct' => false
            ]
        ]);

        $soal8 = Soal::create([
            'id_kategori' => 5,
            'pertanyaan' => 'Apa ya aliha torise sasai ka Bali? Lengkara punika mawasta...'
        ]);

        $soal8->jawabans()->createMany([
            [
                'teks_jawaban' => 'Pawarta',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Patakon',
                'is_correct' => true
            ],
            [
                'teks_jawaban' => 'Patuduh',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'Pakeling',
                'is_correct' => false
            ]
        ]);

        $soal9 = Soal::create([
            'id_kategori' => 1,
            'pertanyaan' => 'Dugase tamasya lebih teken 250 muride macelep ka Museum Bali. Sesuratan 250 tuah...'
        ]);

        $soal9->jawabans()->createMany([
            [
                'teks_jawaban' => '᭒᭕᭐᭞᭞',
                'is_correct' => true
            ],
            [
                'teks_jawaban' => '᭓᭕᭐᭞᭞',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => '᭑᭕᭐᭞᭞',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => '᭒᭓᭐᭞᭞',
                'is_correct' => false
            ]
        ]);

        $soal10 = Soal::create([
            'id_kategori' => 1,
            'pertanyaan' => 'Sesuratan kruna banjar sane patut tuah...'
        ]);

        $soal10->jawabans()->createMany([
            [
                'teks_jawaban' => 'ᬩᬜ᭄ᬚᬃ᭟',
                'is_correct' => true
            ],
            [
                'teks_jawaban' => 'ᬩᬦ᭄ᬚᬃ᭟',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'ᬩᬜ᭄ᬤᬃ᭟',
                'is_correct' => false
            ],
            [
                'teks_jawaban' => 'ᬩᬦ᭄ᬚᬧ᭞᭞',
                'is_correct' => false
            ]
        ]);
        
    }
}
