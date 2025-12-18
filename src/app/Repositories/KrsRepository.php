<?php

namespace App\Repositories;

use App\Models\Krs;
use Illuminate\Support\Facades\DB;

class KrsRepository
{
    public function getOrCreateDraft(int $mahasiswaId, int $tahunAkademikId): Krs
    {
        return Krs::firstOrCreate(
            ['mahasiswa_id' => $mahasiswaId, 'tahun_akademik_id' => $tahunAkademikId],
            ['status' => 'draft', 'total_sks' => 0]
        );
    }

    public function getWithDetail(int $mahasiswaId, ?int $tahunAkademikId = null)
    {
        $q = Krs::query()
            ->with(['detail.kelas.mataKuliah', 'detail.kelas.jadwal'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->orderByDesc('id');

        if ($tahunAkademikId) $q->where('tahun_akademik_id', $tahunAkademikId);

        return $q;
    }

    public function totalSksByKrsId(int $krsId): int
    {
        return (int) DB::table('krs_details')
            ->join('kelas', 'kelas.id', '=', 'krs_details.kelas_id')
            ->join('mata_kuliahs', 'mata_kuliahs.id', '=', 'kelas.mata_kuliah_id')
            ->where('krs_details.krs_id', $krsId)
            ->sum('mata_kuliahs.sks');
    }

    public function jadwalByKrsId(int $krsId)
    {
        return DB::table('jadwals')
            ->join('krs_details', 'krs_details.kelas_id', '=', 'jadwals.kelas_id')
            ->where('krs_details.krs_id', $krsId)
            ->get(['jadwals.hari','jadwals.jam_mulai','jadwals.jam_selesai','jadwals.kelas_id']);
    }
}
