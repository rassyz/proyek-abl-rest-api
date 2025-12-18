<?php

namespace App\Repositories;

use App\Models\Nilai;
use Illuminate\Support\Facades\DB;

class NilaiRepository
{
    public function getMyGradesQuery(int $mahasiswaId, ?int $tahunAkademikId = null)
    {
        $q = Nilai::query()
            ->with(['kelas.mataKuliah', 'kelas.tahunAkademik'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->orderByDesc('id');

        if ($tahunAkademikId) {
            $q->whereHas('kelas', fn($qq) => $qq->where('tahun_akademik_id', $tahunAkademikId));
        }

        return $q;
    }

    public function pesertaKelasPaginated(int $kelasId, int $perPage = 20)
    {
        return DB::table('krs_details')
            ->join('krs', 'krs.id', '=', 'krs_details.krs_id')
            ->join('mahasiswas', 'mahasiswas.id', '=', 'krs.mahasiswa_id')
            ->leftJoin('nilais', function ($join) use ($kelasId) {
                $join->on('nilais.mahasiswa_id', '=', 'mahasiswas.id')
                    ->where('nilais.kelas_id', '=', $kelasId);
            })
            ->where('krs_details.kelas_id', $kelasId)
            ->select([
                'mahasiswas.id as mahasiswa_id',
                'mahasiswas.nim',
                'mahasiswas.nama',
                'nilais.id as nilai_id',
                'nilais.nilai_angka',
                'nilais.nilai_huruf',
                'nilais.bobot',
                'nilais.is_final',
            ])
            ->orderBy('mahasiswa.nama')
            ->paginate($perPage);
    }
}
