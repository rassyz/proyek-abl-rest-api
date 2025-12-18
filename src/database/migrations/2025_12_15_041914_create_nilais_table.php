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
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->cascadeOnDelete();

            $table->decimal('nilai_angka', 5, 2)->nullable();  // 0-100
            $table->string('nilai_huruf', 2)->nullable();      // A/AB/B/...
            $table->decimal('bobot', 3, 2)->nullable();        // 0.00-4.00
            $table->boolean('is_final')->default(false);

            $table->foreignId('dosen_id')->nullable()->constrained('dosens')->nullOnDelete(); // audit
            $table->timestamps();

            $table->unique(['kelas_id', 'mahasiswa_id']);
            $table->index(['mahasiswa_id']);
            $table->index(['kelas_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
