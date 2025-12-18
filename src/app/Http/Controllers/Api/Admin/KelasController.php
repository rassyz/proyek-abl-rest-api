<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kelas\StoreKelasRequest;
use App\Http\Requests\Kelas\UpdateKelasRequest;
use App\Http\Resources\KelasResource;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $tahunId = $request->integer('tahun_akademik_id');
        $mkId = $request->integer('mata_kuliah_id');
        $dosenId = $request->integer('dosen_id');
        $q = $request->string('q')->toString(); // untuk kode kelas / mk kode/nama

        $data = Kelas::query()
            ->with(['tahunAkademik', 'mataKuliah', 'dosen'])
            ->when($tahunId, fn($qq) => $qq->where('tahun_akademik_id', $tahunId))
            ->when($mkId, fn($qq) => $qq->where('mata_kuliah_id', $mkId))
            ->when($dosenId, fn($qq) => $qq->where('dosen_id', $dosenId))
            ->when($q, function ($qq) use ($q) {
                $qq->where('kode_kelas', 'like', "%{$q}%")
                   ->orWhereHas('mataKuliah', fn($m) => $m->where('kode','like',"%{$q}%")->orWhere('nama','like',"%{$q}%"));
            })
            ->orderByDesc('id')
            ->paginate((int) $request->integer('per_page', 10));

        return KelasResource::collection($data);
    }

    public function store(StoreKelasRequest $request)
    {
        $kelas = Kelas::create($request->validated());
        $kelas->load(['tahunAkademik','mataKuliah','dosen']);
        return (new KelasResource($kelas))->response()->setStatusCode(201);
    }

    public function show(Kelas $kelas)
    {
        $kelas->load(['tahunAkademik','mataKuliah','dosen']);
        return new KelasResource($kelas);
    }

    public function update(UpdateKelasRequest $request, Kelas $kelas)
    {
        $kelas->update($request->validated());
        $kelas->load(['tahunAkademik','mataKuliah','dosen']);
        return new KelasResource($kelas);
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return response()->json(['message' => 'Kelas deleted.']);
    }
}
