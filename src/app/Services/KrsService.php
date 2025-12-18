<?php

namespace App\Services;

use App\Models\KrsDetail;
use App\Repositories\KrsRepository;
use App\Repositories\KelasRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KrsService
{
    public function __construct(
        private KrsRepository $krsRepo,
        private KelasRepository $kelasRepo,
    ) {}

    public int $maxSks = 24;

    public function ambil(int $mahasiswaId, int $tahunAkademikId, int $kelasId): KrsDetail
    {
        return DB::transaction(function () use ($mahasiswaId, $tahunAkademikId, $kelasId) {

            $krs = $this->krsRepo->getOrCreateDraft($mahasiswaId, $tahunAkademikId);

            if ($krs->status !== 'draft') {
                throw ValidationException::withMessages([
                    'krs' => ['KRS sudah diajukan/approved, tidak bisa ambil kelas.']
                ]);
            }

            $kelas = $this->kelasRepo->findForKrs($kelasId, $tahunAkademikId);

            if (!$kelas->is_open) {
                throw ValidationException::withMessages([
                    'kelas_id' => ['Kelas tidak dibuka untuk KRS.']
                ]);
            }

            $exists = $krs->detail()->where('kelas_id', $kelasId)->exists();
            if ($exists) {
                throw ValidationException::withMessages([
                    'kelas_id' => ['Kelas sudah diambil.']
                ]);
            }

            if ($kelas->kuota !== null && $kelas->terisi >= $kelas->kuota) {
                throw ValidationException::withMessages([
                    'kelas_id' => ['Kuota kelas penuh.']
                ]);
            }

            // bentrok jadwal
            $jadwalExisting = $this->krsRepo->jadwalByKrsId($krs->id);
            foreach ($kelas->jadwal as $a) {
                foreach ($jadwalExisting as $b) {
                    if ((int)$a->hari !== (int)$b->hari) continue;
                    if ($this->isOverlap($a->jam_mulai, $a->jam_selesai, $b->jam_mulai, $b->jam_selesai)) {
                        throw ValidationException::withMessages([
                            'kelas_id' => ['Bentrok jadwal dengan kelas lain yang sudah diambil.']
                        ]);
                    }
                }
            }

            // batas SKS
            $currentSks = $this->krsRepo->totalSksByKrsId($krs->id);
            $newTotal = $currentSks + (int) $kelas->mataKuliah->sks;
            if ($newTotal > $this->maxSks) {
                throw ValidationException::withMessages([
                    'kelas_id' => ["SKS melebihi batas ({$this->maxSks}). Total menjadi {$newTotal}."]
                ]);
            }

            // prasyarat
            $this->ensurePrasyaratLulus($mahasiswaId, $kelas->mataKuliah->prasyarat->pluck('id')->all());

            $detail = $krs->detail()->create([
                'kelas_id' => $kelasId,
            ]);

            $kelas->increment('terisi');
            $krs->update(['total_sks' => $newTotal]);

            return $detail->load(['kelas.mataKuliah', 'kelas.jadwal']);
        });
    }

    public function drop(int $mahasiswaId, int $krsDetailId): void
    {
        DB::transaction(function () use ($mahasiswaId, $krsDetailId) {
            $detail = \App\Models\KrsDetail::query()
                ->with(['krs', 'kelas.mataKuliah'])
                ->lockForUpdate()
                ->findOrFail($krsDetailId);

            if ((int)$detail->krs->mahasiswa_id !== (int)$mahasiswaId) abort(403);

            if ($detail->krs->status !== 'draft') {
                throw ValidationException::withMessages([
                    'krs' => ['KRS sudah diajukan/approved, tidak bisa drop.']
                ]);
            }

            $kelas = \App\Models\Kelas::query()->lockForUpdate()->findOrFail($detail->kelas_id);

            $sks = (int) $detail->kelas->mataKuliah->sks;

            $detail->delete();
            $kelas->decrement('terisi');

            $detail->krs->update([
                'total_sks' => max(0, (int)$detail->krs->total_sks - $sks)
            ]);
        });
    }

    public function submit(int $mahasiswaId, int $tahunAkademikId): \App\Models\Krs
    {
        return DB::transaction(function () use ($mahasiswaId, $tahunAkademikId) {
            $krs = \App\Models\Krs::query()
                ->where('mahasiswa_id', $mahasiswaId)
                ->where('tahun_akademik_id', $tahunAkademikId)
                ->lockForUpdate()
                ->first();

            if (!$krs) {
                throw ValidationException::withMessages([
                    'krs' => ['Belum ada KRS untuk tahun akademik ini.']
                ]);
            }

            if ($krs->status !== 'draft') {
                throw ValidationException::withMessages([
                    'krs' => ['KRS sudah diajukan/approved.']
                ]);
            }

            $total = $this->krsRepo->totalSksByKrsId($krs->id);
            if ($total <= 0) {
                throw ValidationException::withMessages([
                    'krs' => ['KRS kosong. Ambil minimal 1 kelas.']
                ]);
            }

            $krs->update(['status' => 'submitted', 'total_sks' => $total]);

            return $krs->load(['detail.kelas.mataKuliah', 'detail.kelas.jadwal']);
        });
    }

    private function isOverlap(string $startA, string $endA, string $startB, string $endB): bool
    {
        return ($startA < $endB) && ($startB < $endA);
    }

    private function ensurePrasyaratLulus(int $mahasiswaId, array $prasyaratIds): void
    {
        if (count($prasyaratIds) === 0) return;

        $lulusIds = DB::table('nilais')
            ->join('kelas', 'kelas.id', '=', 'nilais.kelas_id')
            ->where('nilais.mahasiswa_id', $mahasiswaId)
            ->whereIn('kelas.mata_kuliah_id', $prasyaratIds)
            ->whereNotNull('nilais.bobot')
            ->where('nilais.bobot', '>', 0)
            ->pluck('kelas.mata_kuliah_id')
            ->unique()
            ->all();

        $missing = array_values(array_diff($prasyaratIds, $lulusIds));
        if (count($missing) > 0) {
            throw ValidationException::withMessages([
                'kelas_id' => ['Prasyarat mata kuliah belum terpenuhi.']
            ]);
        }
    }
}
