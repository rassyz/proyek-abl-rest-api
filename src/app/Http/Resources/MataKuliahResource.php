<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MataKuliahResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'kode'          => $this->kode,
            'nama'          => $this->nama,
            'sks'           => $this->sks,
            'semester_saran'=> $this->semester_saran,
            'prodi'         => $this->whenLoaded('prodi', fn() => [
                'id'            => $this->prodi->id,
                'kode'          => $this->prodi->kode,
                'nama'          => $this->prodi->nama,
            ]),
            'prasyarat'     => $this->whenLoaded('prasyarat', function () {
                return $this->prasyarat->map(fn($mk) => [
                    'id'            => $mk->id,
                    'kode'          => $mk->kode,
                    'nama'          => $mk->nama,
                ]);
            }),
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }
}
