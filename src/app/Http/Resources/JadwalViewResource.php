<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JadwalViewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'jadwal_id'     => $this->jadwal_id,
            'hari'          => $this->hari,
            'jam_mulai'     => $this->jam_mulai,
            'jam_selesai'   => $this->jam_selesai,
            'kelas_id'      => $this->kelas_id,
            'kode_kelas'    => $this->kode_kelas,
            'mk_kode'       => $this->mk_kode,
            'mk_nama'       => $this->mk_nama,
            'dosen'         => $this->dosen,
            'ruangan_kode'  => $this->ruangan_kode,
        ];
    }
}
