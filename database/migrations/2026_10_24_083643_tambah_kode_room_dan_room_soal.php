<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('rooms', 'kode_room')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->string('kode_room', 10)->nullable()->after('nama_room');
            });
 
            // Room assessment lama belum punya kode: beri satu per room.
            // Room battle sengaja dikosongkan (tidak memakai kode).
            $huruf = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
 
            foreach (DB::table('rooms')->whereNull('kode_room')->where('mode', 'assessment')->get() as $room) {
                do {
                    $kode = '';
                    for ($i = 0; $i < 6; $i++) {
                        $kode .= $huruf[random_int(0, strlen($huruf) - 1)];
                    }
                } while (DB::table('rooms')->where('kode_room', $kode)->exists());
 
                DB::table('rooms')->where('id_room', $room->id_room)->update(['kode_room' => $kode]);
            }
        }
 
        // ---------- 2. Tabel penghubung room_soal ----------
        if (! Schema::hasTable('room_soal')) {
            Schema::create('room_soal', function (Blueprint $table) {
                // Room dihapus -> baris penghubungnya ikut terhapus
                $table->foreignId('id_room')->constrained('rooms', 'id_room')->cascadeOnDelete();
 
                // Soal yang masih dipakai room TIDAK bisa dihapus
                $table->foreignId('id_soal')->constrained('soals', 'id_soal')->restrictOnDelete();
 
                // agar tidak ada duplikasi soal di room yang sama
                $table->primary(['id_room', 'id_soal']);
            });
        }
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            //
        });
    }
};
