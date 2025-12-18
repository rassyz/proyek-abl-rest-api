<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Nilai\FinalisasiNilaiRequest;
use App\Http\Requests\Nilai\StoreNilaiBatchRequest;
use App\Http\Resources\NilaiResource;
use App\Models\Kelas;
use App\Services\NilaiService;
use App\Repositories\NilaiRepository;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function __construct(
        private NilaiService $service,
        private NilaiRepository $repo
    ) {}

    public function indexMyGrades(Request $request)
    {
        $mhsId = $request->user()->mahasiswa->id;
        $tahunId = $request->integer('tahun_akademik_id');

        $q = $this->repo->getMyGradesQuery($mhsId, $tahunId);

        return NilaiResource::collection(
            $q->paginate((int) $request->integer('per_page', 10))
        );
    }

    public function indexByKelas(Kelas $kelas, Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');
        $dosenId = $user->dosen?->id;

        if (!$isAdmin && (int)$kelas->dosen_id !== (int)$dosenId) abort(403);

        $data = $this->service->listPesertaKelas($kelas->id, (int)$request->integer('per_page', 20));
        return response()->json(['data' => $data]);
    }

    public function storeBatch(StoreNilaiBatchRequest $request)
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');
        $currentDosenId = $user->dosen?->id;
        $auditDosenId = $user->dosen?->id; // untuk admin bisa null (atau set dosen input manual)

        $payload = $request->validated();

        $this->service->storeBatch(
            $payload['kelas_id'],
            $payload['items'],
            $isAdmin,
            $currentDosenId,
            $auditDosenId
        );

        return response()->json(['message' => 'Nilai berhasil disimpan.'], 201);
    }

    public function finalisasi(FinalisasiNilaiRequest $request)
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');
        $currentDosenId = $user->dosen?->id;

        $payload = $request->validated();

        $this->service->finalisasi(
            $payload['kelas_id'],
            $payload['is_final'],
            $isAdmin,
            $currentDosenId
        );

        return response()->json(['message' => 'Finalisasi nilai berhasil.']);
    }
}
