<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NilaiResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'kelas_id'      => $this->kelas_id,
            'mahasiswa_id'  => $this->mahasiswa_id,
            'nilai_angka'   => $this->nilai_angka,
            'nilai_huruf'   => $this->nilai_huruf,
            'bobot'         => $this->bobot,
            'is_final'      => (bool) $this->is_final,
            'kelas'         => $this->whenLoaded('kelas', function () {
                return [
                    'id'            => $this->kelas->id,
                    'kode_kelas'    => $this->kelas->kode_kelas,
                    'mata_kuliah'   => $this->kelas->relationLoaded('mataKuliah') ? [
                        'id'            => $this->kelas->mataKuliah->id,
                        'kode'          => $this->kelas->mataKuliah->kode,
                        'nama'          => $this->kelas->mataKuliah->nama,
                        'sks'           => $this->kelas->mataKuliah->sks,
                    ] : null,
                ];
            }),
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }
}
