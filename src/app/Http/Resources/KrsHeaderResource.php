<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KrsHeaderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'tahun_akademik_id' => $this->tahun_akademik_id,
            'status'            => $this->status,
            'total_sks'         => $this->total_sks,
            'detail'            => KrsDetailResource::collection($this->whenLoaded('detail')),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
        ];
    }
}
