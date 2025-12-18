<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Nilai;
use App\Repositories\NilaiRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NilaiService
{
    public function __construct(private NilaiRepository $nilaiRepo) {}

    public function listPesertaKelas(int $kelasId, int $perPage = 20)
    {
        return $this->nilaiRepo->pesertaKelasPaginated($kelasId, $perPage);
    }

    public function storeBatch(int $kelasId, array $items, bool $isAdmin, ?int $currentDosenId, ?int $auditDosenId): void
    {
        $kelas = Kelas::query()->findOrFail($kelasId);

        if (!$isAdmin && (int)$kelas->dosen_id !== (int)$currentDosenId) {
            abort(403, 'Forbidden');
        }

        DB::transaction(function () use ($kelasId, $items, $auditDosenId) {
            foreach ($items as $row) {
                $mhsId = (int)$row['mahasiswa_id'];
                $nilaiAngka = (float)$row['nilai_angka'];

                $existing = Nilai::query()
                    ->where('kelas_id', $kelasId)
                    ->where('mahasiswa_id', $mhsId)
                    ->first();

                if ($existing && $existing->is_final) {
                    throw ValidationException::withMessages([
                        'items' => ['Ada nilai yang sudah FINAL, tidak bisa diubah.']
                    ]);
                }

                [$huruf, $bobot] = $this->konversiNilai($nilaiAngka);

                Nilai::updateOrCreate(
                    ['kelas_id' => $kelasId, 'mahasiswa_id' => $mhsId],
                    [
                        'nilai_angka' => $nilaiAngka,
                        'nilai_huruf' => $huruf,
                        'bobot' => $bobot,
                        'is_final' => false,
                        'dosen_id' => $auditDosenId,
                    ]
                );
            }
        });
    }

    public function finalisasi(int $kelasId, bool $isFinal, bool $isAdmin, ?int $currentDosenId): void
    {
        $kelas = Kelas::query()->findOrFail($kelasId);

        if (!$isAdmin && (int)$kelas->dosen_id !== (int)$currentDosenId) {
            abort(403, 'Forbidden');
        }

        Nilai::query()
            ->where('kelas_id', $kelasId)
            ->update(['is_final' => $isFinal]);
    }

    public function konversiNilai(float $nilai): array
    {
        if ($nilai >= 85) return ['A', 4.00];
        if ($nilai >= 80) return ['AB', 3.50];
        if ($nilai >= 75) return ['B', 3.00];
        if ($nilai >= 70) return ['BC', 2.50];
        if ($nilai >= 65) return ['C', 2.00];
        if ($nilai >= 50) return ['D', 1.00];
        return ['E', 0.00];
    }
}
