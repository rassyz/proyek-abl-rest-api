<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'nip'           => $this->nip,
            'nama'          => $this->nama,
            'bagian'        => $this->bagian,
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
