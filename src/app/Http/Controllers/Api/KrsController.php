<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Krs\AmbilKrsRequest;
use App\Http\Requests\Krs\SubmitKrsRequest;
use App\Http\Resources\KrsDetailResource;
use App\Http\Resources\KrsHeaderResource;
use App\Models\KrsDetail;
use App\Services\KrsService;
use Illuminate\Http\Request;

class KrsController extends Controller
{
    public function __construct(
        private KrsService $service
    ) {}

    public function index(Request $request)
    {
        $mahasiswaId = $request->user()->mahasiswa->id;
        $tahunId = $request->integer('tahun_akademik_id');

        $query = app(\App\Repositories\KrsRepository::class)
            ->getWithDetail($mahasiswaId, $tahunId);

        return KrsHeaderResource::collection(
            $query->paginate((int) $request->integer('per_page', 10))
        );
    }

    public function ambil(AmbilKrsRequest $request)
    {
        $mahasiswaId = $request->user()->mahasiswa->id;
        $payload = $request->validated();

        $detail = $this->service->ambil(
            $mahasiswaId,
            $payload['tahun_akademik_id'],
            $payload['kelas_id']
        );

        return (new KrsDetailResource($detail))->response()->setStatusCode(201);
    }

    public function drop(Request $request, KrsDetail $krsDetail)
    {
        $mahasiswaId = $request->user()->mahasiswa->id;

        $this->service->drop($mahasiswaId, $krsDetail->id);

        return response()->json(['message' => 'Kelas berhasil di-drop.']);
    }

    public function submit(SubmitKrsRequest $request)
    {
        $mahasiswaId = $request->user()->mahasiswa->id;
        $payload = $request->validated();

        $krs = $this->service->submit($mahasiswaId, $payload['tahun_akademik_id']);

        return new KrsHeaderResource($krs);
    }
}
