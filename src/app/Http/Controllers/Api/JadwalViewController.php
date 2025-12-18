<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JadwalViewResource;
use App\Services\JadwalService;
use Illuminate\Http\Request;

class JadwalViewController extends Controller
{
    public function __construct(private JadwalService $service) {}

    public function index(Request $request)
    {
        $tahunId = $request->integer('tahun_akademik_id');

        $query = $this->service->listForUser($request->user(), $tahunId);

        $data = $query
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->paginate((int) $request->integer('per_page', 20));

        return JadwalViewResource::collection($data);
    }
}
