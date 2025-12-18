<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TahunAkademikResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'tahun'         => $this->tahun,
            'semester'      => $this->semester,
            'is_active'     => (bool) $this->is_active,
            'mulai'         => $this->mulai,
            'selesai'       => $this->selesai,
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }
}
