<?php

namespace App\Repositories;

use App\Models\Kelas;

class KelasRepository
{
    public function findForKrs(int $kelasId, int $tahunAkademikId): Kelas
    {
        return Kelas::query()
            ->with(['mataKuliah.prasyarat', 'jadwal'])
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->lockForUpdate()
            ->findOrFail($kelasId);
    }
}
