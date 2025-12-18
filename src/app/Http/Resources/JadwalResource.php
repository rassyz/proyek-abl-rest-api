<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JadwalResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'hari'          => $this->hari, // 1-7
            'jam_mulai'     => $this->jam_mulai,
            'jam_selesai'   => $this->jam_selesai,

            'ruangan'       => $this->whenLoaded('ruangan', fn() => $this->ruangan ? [
                'id'            => $this->ruangan->id,
                'kode'          => $this->ruangan->kode,
                'nama'          => $this->ruangan->nama,
            ] : null),

            'kelas'         => $this->whenLoaded('kelas', fn() => [
                'id'            => $this->kelas->id,
                'kode_kelas'    => $this->kelas->kode_kelas,
                'dosen'         => $this->kelas->dosen->nama,
                'mata_kuliah'   => $this->kelas->mataKuliah->nama,
            ]),

            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }
}
