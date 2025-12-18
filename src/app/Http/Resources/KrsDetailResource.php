<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KrsDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        $kelas = $this->whenLoaded('kelas');

        return [
            'id'        => $this->id,
            'krs_id'    => $this->krs_id,
            'kelas_id'  => $this->kelas_id,
            'kelas'     => $kelas ? [
                'id'            => $kelas->id,
                'kode_kelas'    => $kelas->kode_kelas,
                'kuota'         => $kelas->kuota,
                'terisi'        => $kelas->terisi,
                'is_open'       => (bool) $kelas->is_open,
                'mata_kuliah'   => $kelas->relationLoaded('mataKuliah') ? [
                    'id'            => $kelas->mataKuliah->id,
                    'kode'          => $kelas->mataKuliah->kode,
                    'nama'          => $kelas->mataKuliah->nama,
                    'sks'           => $kelas->mataKuliah->sks,
                ] : null,
                'jadwal'        => $kelas->relationLoaded('jadwal')
                    ? $kelas->jadwal->map(fn($j) => [
                        'id'            => $j->id,
                        'hari'          => $j->hari,
                        'jam_mulai'     => $j->jam_mulai,
                        'jam_selesai'   => $j->jam_selesai,
                        'ruangan_id'    => $j->ruangan_id,
                    ])
                    : null,
            ] : null,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
