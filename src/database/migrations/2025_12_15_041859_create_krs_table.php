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
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();
            $table->foreignId('tahun_akademik_id')->constrained('tahun_akademiks')->restrictOnDelete();
            $table->string('status')->default('draft'); // draft|submitted|approved|rejected
            $table->unsignedTinyInteger('total_sks')->default(0);
            $table->timestamps();

            $table->unique(['mahasiswa_id', 'tahun_akademik_id']);
            $table->index(['tahun_akademik_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};
