<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kategori::create([
            'nama_kategori' => 'Aksara Bali'
        ]);

        Kategori::create([
            'nama_kategori' => 'Anggah-ungguh'
        ]);

        Kategori::create([
            'nama_kategori' => 'Beburonan'
        ]);

        Kategori::create([
            'nama_kategori' => 'Kasusastran'
        ]);

        Kategori::create([
            'nama_kategori' => 'Soroh-Soroh Lengkara'
        ]);
    }
}
