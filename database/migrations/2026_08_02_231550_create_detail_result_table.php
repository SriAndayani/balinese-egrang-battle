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
        Schema::create('detail_results', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_ass')
                ->constrained('assessment_results', 'id_ass')
                ->cascadeOnDelete();
            $table->foreignId('id_soal')
                ->constrained('soals', 'id_soal')
                ->cascadeOnDelete();
            $table->foreignId('id_jawaban')
                ->constrained('jawabans', 'id_jawaban')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_result');
    }
};
