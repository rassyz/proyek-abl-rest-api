<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KelasResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'kode_kelas'    => $this->kode_kelas,
            'kuota'         => $this->kuota,
            'terisi'        => $this->terisi,
            'is_open'       => (bool) $this->is_open,

            'tahun_akademik' => $this->whenLoaded('tahunAkademik', fn() => [
                'id'            => $this->tahunAkademik->id,
                'tahun'         => $this->tahunAkademik->tahun,
                'semester'      => $this->tahunAkademik->semester,
                'is_active'     => (bool) $this->tahunAkademik->is_active,
            ]),

            'mata_kuliah' => $this->whenLoaded('mataKuliah', fn() => [
                'id'            => $this->mataKuliah->id,
                'kode'          => $this->mataKuliah->kode,
                'nama'          => $this->mataKuliah->nama,
                'sks'           => $this->mataKuliah->sks,
            ]),

            'dosen'     => $this->whenLoaded('dosen', fn() => [
                'id'        => $this->dosen->id,
                'nidn'      => $this->dosen->nidn,
                'nama'      => $this->dosen->nama,
            ]),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
