<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MataKuliah\StoreMataKuliahRequest;
use App\Http\Requests\MataKuliah\UpdateMataKuliahRequest;
use App\Http\Requests\MataKuliah\SyncPrasyaratRequest;
use App\Http\Resources\MataKuliahResource;
use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $prodiId = $request->integer('prodi_id');

        $data = MataKuliah::query()
            ->with(['prodi', 'prasyarat'])
            ->when($prodiId, fn($qq) => $qq->where('prodi_id', $prodiId))
            ->when($q, function ($qq) use ($q) {
                $qq->where('kode', 'like', "%{$q}%")
                   ->orWhere('nama', 'like', "%{$q}%");
            })
            ->orderBy('kode')
            ->paginate((int) $request->integer('per_page', 10));

        return MataKuliahResource::collection($data);
    }

    public function store(StoreMataKuliahRequest $request)
    {
        $mk = MataKuliah::create($request->validated());
        $mk->load(['prodi', 'prasyarat']);
        return (new MataKuliahResource($mk))->response()->setStatusCode(201);
    }

    public function show(MataKuliah $mata_kuliah)
    {
        $mata_kuliah->load(['prodi', 'prasyarat']);
        return new MataKuliahResource($mata_kuliah);
    }

    public function update(UpdateMataKuliahRequest $request, MataKuliah $mata_kuliah)
    {
        $mata_kuliah->update($request->validated());
        $mata_kuliah->load(['prodi', 'prasyarat']);
        return new MataKuliahResource($mata_kuliah);
    }

    public function destroy(MataKuliah $mata_kuliah)
    {
        $mata_kuliah->prasyarat()->detach();
        $mata_kuliah->delete();
        return response()->json(['message' => 'Mata kuliah deleted.']);
    }

    public function syncPrasyarat(SyncPrasyaratRequest $request, MataKuliah $mata_kuliah)
    {
        $ids = $request->validated()['prasyarat_ids'];

        // cegah self-prasyarat
        $ids = array_values(array_filter($ids, fn($id) => (int)$id !== (int)$mata_kuliah->id));

        $mata_kuliah->prasyarat()->sync($ids);
        $mata_kuliah->load(['prodi', 'prasyarat']);

        return new MataKuliahResource($mata_kuliah);
    }
}
