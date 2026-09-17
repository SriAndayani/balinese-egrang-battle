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
        Schema::create('room_soal', function (Blueprint $table) {
            $table->id('id_rs');
            $table->foreignId('id_room')
                ->constrained('rooms', 'id_room')
                ->cascadeOnDelete();

            $table->foreignId('id_soal')
                ->constrained('soals', 'id_soal')
                ->cascadeOnDelete();
            $table->unique(['id_room', 'id_soal']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_soal');
    }
};
