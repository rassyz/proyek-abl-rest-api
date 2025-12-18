<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DosenResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'nidn'          => $this->nidn,
            'nama'          => $this->nama,
            'no_hp'         => $this->no_hp,
            'prodi'         => $this->whenLoaded('prodi', fn() => [
                'id'            => $this->prodi->id,
                'kode'          => $this->prodi->kode,
                'nama'          => $this->prodi->nama,
            ]),
            'user'          => $this->whenLoaded('user', fn() => [
                'id'            => $this->user->id,
                'name'          => $this->user->name,
                'email'         => $this->user->email,
                'roles'         => $this->user->getRoleNames(),
            ]),
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }
}
