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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->restrictOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->restrictOnDelete();
            $table->foreignId('dosen_id')->constrained('dosens')->restrictOnDelete();
            $table->string('kode_kelas'); // A/B/C
            $table->unsignedSmallInteger('kuota')->nullable();
            $table->unsignedSmallInteger('terisi')->default(0);
            $table->boolean('is_open')->default(true);
            $table->timestamps();

            $table->unique(['tahun_akademik_id', 'mata_kuliah_id', 'kode_kelas']);
            $table->index(['tahun_akademik_id', 'dosen_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
