<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class JadwalRepository
{
    public function baseQuery()
    {
        return DB::table('jadwals')
            ->join('kelas', 'kelas.id', '=', 'jadwals.kelas_id')
            ->join('mata_kuliahs', 'mata_kuliahs.id', '=', 'kelas.mata_kuliah_id')
            ->leftJoin('ruangans', 'ruangans.id', '=', 'jadwals.ruangan_id')
            ->leftJoin('dosens', 'dosens.id', '=', 'kelas.dosen_id')
            ->select([
                'jadwals.id as jadwal_id',
                'jadwals.hari',
                'jadwals.jam_mulai',
                'jadwals.jam_selesai',
                'kelas.id as kelas_id',
                'kelas.kode_kelas',
                'dosens.nama as dosen',
                'mata_kuliahs.kode as mk_kode',
                'mata_kuliahs.nama as mk_nama',
                'ruangans.kode as ruangan_kode',
            ]);
    }

    public function forAdmin(?int $tahunAkademikId)
    {
        $q = $this->baseQuery();
        if ($tahunAkademikId) $q->where('kelas.tahun_akademik_id', $tahunAkademikId);
        return $q;
    }

    public function forDosen(int $dosenId, ?int $tahunAkademikId)
    {
        $q = $this->baseQuery()->where('kelas.dosen_id', $dosenId);
        if ($tahunAkademikId) $q->where('kelas.tahun_akademik_id', $tahunAkademikId);
        return $q;
    }

    public function forMahasiswa(int $mahasiswaId, ?int $tahunAkademikId)
    {
        $q = $this->baseQuery()
            ->join('krs_details', 'krs_details.kelas_id', '=', 'kelas.id')
            ->join('krs', 'krs.id', '=', 'krs_details.krs_id')
            ->where('krs.mahasiswa_id', $mahasiswaId);

        if ($tahunAkademikId) $q->where('krs.tahun_akademik_id', $tahunAkademikId);

        return $q;
    }
}
